/**
 * @type: saga
 * name: comment.viewMoreComments
 */

import {
  getItem,
  pagination,
  patchEntity,
  getResourceAction,
  handleActionError,
  getGlobalContext,
  fulfillEntity
} from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';
import {
  SORT_MODE_DESC,
  SortTypeValue,
  SORT_MODE_ASC,
  SORT_RELEVANT
} from '@metafox/comment';
import { isEmpty, uniq } from 'lodash';
import {
  getValueSortTypeMode,
  getDataBySortTypeMode,
  getDataBySortType,
  getKeyDataBySortType
} from '@metafox/comment/utils';

type ViewMoreCommentAction = {
  type: string;
  payload: {
    identity: string;
    sortType: SortTypeValue;
    sortSettingDefault: SortTypeValue;
    pagingId?: string;
  };
};

type ViewMoreCommentSuccessAction = {
  type: string;
  payload: { identity: string; sortType: SortTypeValue };
  meta: { ids: string[]; loadmoreFinish: () => void };
};

export function* viewMoreComments(action: ViewMoreCommentAction) {
  const {
    identity,
    sortType,
    sortSettingDefault,
    pagingId: pagingIdProp
  } = action.payload;
  const { loadmoreFinish } = action?.meta || {};
  const viewmoreSortTypeMode = getValueSortTypeMode(sortType);
  const sortTypeModeDefault = getValueSortTypeMode(sortSettingDefault);
  const pagingId =
    pagingIdProp ||
    `comment/${viewmoreSortTypeMode}/${identity.replace(/\./g, '_')}`;

  if (!identity) return;

  const item = yield* getItem(identity);

  if (!item) return null;

  const {
    comment_type_id,
    item_type,
    resource_name,
    comment_item_id,
    item_id,
    id,
    related_comments,
    _oldest_related_comments,
    _newest_related_comments
  } = item;

  const excludesComment = item.excludesComment ?? [];
  const relevant_comments = item.relevant_comments ?? [];
  const dataCurrent =
    getDataBySortTypeMode(item, viewmoreSortTypeMode) ||
    (relevant_comments.length
      ? relevant_comments.slice(1)
      : related_comments) ||
    [];

  if (SORT_RELEVANT !== sortType) {
    if (
      sortTypeModeDefault === SORT_MODE_ASC &&
      isEmpty(_oldest_related_comments)
    ) {
      yield* patchEntity(identity, {
        _oldest_related_comments: related_comments
      });
    }

    if (
      sortTypeModeDefault === SORT_MODE_DESC &&
      isEmpty(_newest_related_comments)
    ) {
      yield* patchEntity(identity, {
        _newest_related_comments: related_comments
      });
    }
  }

  const last_id =
    dataCurrent[dataCurrent.length - 1] &&
    typeof dataCurrent[dataCurrent.length - 1] === 'string'
      ? dataCurrent[dataCurrent.length - 1].split('.')[3]
      : undefined;

  yield* pagination(
    {
      pagingId,
      apiUrl: '/comment',
      lastIdMode: true,
      apiParams: {
        item_type: comment_type_id || item_type || resource_name,
        item_id: comment_item_id || item_id || id,
        limit: 10,
        excludes: excludesComment,
        last_id,
        sort_type: viewmoreSortTypeMode
      }
    },
    {
      successAction: {
        type: 'comment/viewMoreComments/SUCCESS',
        payload: { identity, sortType, sortSettingDefault },
        meta: { loadmoreFinish }
      }
    }
  );
}

export function* viewMoreReplies(action: ViewMoreCommentAction) {
  const { payload } = action;
  const { identity, pagingId: pagingIdProp } = payload;
  const pagingId = pagingIdProp || `comment/${identity.replace(/\./g, '_')}`;

  if (!identity) return;

  const item = yield* getItem(identity);

  if (!item) return;

  const { children } = item;

  const item_type =
    item.comment_item_type || item.item_type || item.resource_name;
  const item_id = item.comment_item_id || item.item_id || item.id;

  const excludesComment = item.excludesComment ?? [];

  const last_id =
    children &&
    children[children.length - 1] &&
    typeof children[children.length - 1] === 'string'
      ? children[children.length - 1].split('.')[3]
      : undefined;

  yield* pagination(
    {
      pagingId,
      apiUrl: '/comment',
      lastIdMode: true,
      apiParams: {
        item_type,
        item_id,
        parent_id: item.id,
        limit: 10,
        excludes: excludesComment,
        sort_type: SORT_MODE_DESC,
        last_id
      }
    },
    {
      successAction: {
        type: 'comment/viewMoreComments/SUCCESS',
        payload: { identity }
      }
    }
  );
}

function* viewMoreCommentsSuccess(action: ViewMoreCommentSuccessAction) {
  const { identity, sortType } = action.payload;
  const { ids, loadmoreFinish } = action.meta;
  const item = yield* getItem(identity);

  if (!item) return null;

  if (!ids) return null;

  let oldComments = item.children || [];

  if ('comment' !== item.resource_name) {
    oldComments = getDataBySortType(item, sortType, true) || [];
  }

  const keyMapping = getKeyDataBySortType(sortType, false);
  const data =
    'comment' === item.resource_name
      ? {
          children: uniq(oldComments.concat(ids) ?? [])
        }
      : {
          // eslint-disable-next-line max-len
          [keyMapping]: uniq(oldComments.concat(ids) ?? [])
        };
  yield* patchEntity(identity, data);

  if (loadmoreFinish) {
    loadmoreFinish();
  }
}

function* changeSort({ payload, meta }: ItemLocalAction) {
  const { identity, sortType, excludes } = payload;
  const item = yield* getItem(identity);
  const viewmoreSortTypeMode = getValueSortTypeMode(sortType);
  const data = getDataBySortTypeMode(item, viewmoreSortTypeMode, true);
  const keyDataDefault = getKeyDataBySortType(sortType, true);
  const keyDataFull = getKeyDataBySortType(sortType, false);
  const { apiClient, compactUrl, compactData, normalization, getSetting } =
    yield* getGlobalContext();
  const totalPrefetchCommentsDefault: number = getSetting(
    'comment.prefetch_comments_on_feed'
  );

  if (!item || !isEmpty(data) || totalPrefetchCommentsDefault === 0) {
    if (meta?.onSuccess) {
      meta.onSuccess();
    }

    return;
  }

  const config = yield* getResourceAction(
    'comment',
    'comment',
    'getRelatedComments'
  );

  const {
    comment_type_id,
    item_type,
    resource_name,
    comment_item_id,
    item_id,
    id
  } = item;

  if (!config?.apiUrl) return;

  try {
    const response = yield apiClient.request({
      method: config?.apiMethod || 'get',
      url: compactUrl(config.apiUrl, item),
      params: compactData(config.apiParams, {
        ...item,
        item_type: comment_type_id || item_type || resource_name,
        item_id: comment_item_id || item_id || id,
        sort_type: getValueSortTypeMode(sortType),
        excludes
      })
    });

    if (response?.data) {
      const data = response.data?.data;
      const result = normalization.normalize(data);
      yield* fulfillEntity(result.data);
      yield* patchEntity(identity, {
        [keyDataDefault]: result.ids ?? [],
        [keyDataFull]: result.ids ?? []
      });

      if (meta?.onSuccess) {
        meta.onSuccess();
      }
    }

    return true;
  } catch (error) {
    yield* handleActionError(error);
  }

  return false;
}

const sagas = [
  takeEvery('comment/viewMoreComments', viewMoreComments),
  takeEvery('comment/viewMoreReplies', viewMoreReplies),
  takeEvery('comment/viewMoreComments/SUCCESS', viewMoreCommentsSuccess),
  takeEvery('comment/changeSort', changeSort)
];

export default sagas;
