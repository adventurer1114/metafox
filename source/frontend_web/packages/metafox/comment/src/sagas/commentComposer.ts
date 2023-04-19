/**
 * @type: saga
 * name: comment.commentComposer
 */

import {
  APP_BOOTSTRAP,
  fulfillEntity,
  getGlobalContext,
  getItem,
  handleActionError,
  handleActionFeedback,
  patchEntity
} from '@metafox/framework';
import { omit, get } from 'lodash';
import { actionChannel, call, put, take, takeEvery } from 'redux-saga/effects';

type AttachNewCommentAction = {
  type: string;
  payload: {
    identity: string;
    comment: string;
    feed_id: number;
    identityResource?: string;
  };
};

export function* updateFeed(
  identity: string,
  result: { total_comment: number }
) {
  const item = yield* getItem(identity);

  if (!item) return;

  const total_comment = result.total_comment;
  const statistic = item.statistic
    ? { ...item.statistic, total_comment }
    : { total_comment };

  yield* patchEntity(identity, { statistic });
}

export function* attachNewComment(action: AttachNewCommentAction) {
  const { identity, comment, feed_id, identityResource } = action.payload;
  const item = yield* getItem(identity);
  const feedItem = yield* getItem(`feed.entities.feed.${feed_id}`);
  const resourceItem = yield* getItem(identityResource);

  if (!item) return;

  const statistic = item.statistic ?? {};

  if ('comment' === item.resource_name) {
    const childrenData = item?.children || [];
    const comments = [comment, ...childrenData];

    yield* patchEntity(identity, {
      children: comments,
      child_total: item.child_total ? item.child_total + 1 : 1
    });

    if (resourceItem && identityResource !== `feed.entities.feed.${feed_id}`) {
      const statistic = resourceItem.statistic ?? {};

      yield* patchEntity(resourceItem._identity, {
        statistic: {
          ...statistic,
          total_reply: statistic.total_reply + 1,
          total_comment: statistic.total_comment + 1
        }
      });
    }

    if (!feedItem) return;

    const statistic = feedItem.statistic ?? {};

    yield* patchEntity(feedItem._identity, {
      statistic: {
        ...statistic,
        total_reply: statistic.total_reply + 1,
        total_comment: statistic.total_comment + 1
      }
    });
  } else {
    const commentsNew = (item?.commentsNew || []).concat(comment);
    const commentNewId = comment.split('.')[3];
    const excludeIds = (item?.excludesComment || []).concat(commentNewId);

    yield* patchEntity(identity, {
      commentsNew,
      excludesComment: excludeIds,
      statistic: {
        ...statistic,
        total_comment: statistic.total_comment + 1
      }
    });
  }
}

export function* preFetchComment(action) {
  const { text, identity } = action.payload;
  const item = yield* getItem(identity);

  const { preFetchingComment: defaultValue = {} } = item;

  const commentKey = Object.keys(defaultValue || {}).length;

  const mentionReg =
    /\[(?:user|page|group)=(\d+)\]([^[]+)\[\/(?:user|page|group)\]/gm;

  const newComment = {
    text: text?.replace(mentionReg, '$2'),
    isLoading: true
  };

  yield* patchEntity(identity, {
    preFetchingComment: { ...defaultValue, [commentKey]: newComment }
  });
  yield put({
    type: 'comment/composer/CALL',
    payload: { ...action.payload, commentKey },
    meta: action?.meta
  });
}

export function* postComment({ payload, meta }) {
  const {
    text,
    identity,
    sticker_id,
    photo_id,
    editing,
    commentKey,
    oldText,
    identityResource,
    captchaData = {},
    formCaptcha
  } = payload;
  const item = yield* getItem(identity);
  const { apiClient, normalization } = yield* getGlobalContext();

  // check if item is no identity

  if (!item) return null;

  const {
    comment_type_id,
    comment_item_id,
    item_type,
    item_id,
    id,
    resource_name
  } = item;

  const customData = {
    text,
    photo_id: photo_id !== 0 ? photo_id : undefined,
    item_id: comment_item_id || item_id || id,
    item_type: comment_type_id || item_type || resource_name
  };

  const parent_id = 'comment' === item.resource_name ? item.id : 0;

  try {
    const response = editing
      ? yield apiClient.request({
          url: `/comment/${item.id}`,
          method: 'PUT',
          data: {
            ...customData,
            attach_changed: 1,
            sticker_id
          }
        })
      : yield apiClient.request({
          url: '/comment',
          method: 'POST',
          data: { ...customData, sticker_id, parent_id, ...captchaData }
        });

    if (commentKey > -1) {
      yield* patchEntity(
        identity,
        {
          preFetchingComment: {
            [commentKey]: { isLoading: false }
          }
        },
        true
      );
    }

    const result = response.data.data;
    const feed_id = response.data?.meta?.feed_id;
    const responseItem = result?.item || result; // support both v4, v5
    const normalized = normalization.normalize(responseItem);

    yield* handleActionFeedback(response);

    if (editing) {
      yield* fulfillEntity(omit(normalized.data, ['user']));
    } else {
      yield* fulfillEntity(normalized.data);

      yield* attachNewComment({
        type: '',
        payload: {
          identity,
          comment: normalized.ids[0],
          feed_id,
          identityResource
        }
      });

      if (result?.feed_id) {
        yield* updateFeed(`feed.entities.feed.${result.feed_id}`, result);
      }
    }

    if (meta?.onSuccess) {
      meta.onSuccess();
    }
  } catch (error) {
    yield* handleActionError(error, formCaptcha);

    if (formCaptcha) {
      formCaptcha.setSubmitting(false);
    }

    if (meta?.onCancel) {
      const errors = get(error, 'response.data.errors');
      meta.onCancel(errors);
    }

    if (editing) {
      yield* patchEntity(identity, {
        text: oldText
      });
    }

    if (commentKey > -1) {
      yield* patchEntity(
        identity,
        {
          preFetchingComment: {
            [commentKey]: { isLoading: false }
          }
        },
        true
      );
    }
  }
}

// make call api queue
function* watchCommentCall() {
  const requestChanelCall = yield actionChannel('comment/composer/CALL');

  while (true) {
    const { payload, meta } = yield take(requestChanelCall);
    yield call(postComment, { payload, meta });
  }
}

const sagas = [
  takeEvery('comment/composer/CREATE', preFetchComment),
  takeEvery(APP_BOOTSTRAP, watchCommentCall)
];

export default sagas;
