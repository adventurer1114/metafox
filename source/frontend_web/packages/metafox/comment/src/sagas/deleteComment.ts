/**
 * @type: saga
 * name: comment.saga.deleteComment
 */

import {
  deleteEntity,
  getGlobalContext,
  getItem,
  getItemActionConfig,
  handleActionConfirm,
  handleActionError,
  handleActionFeedback,
  ItemLocalAction,
  patchEntity
} from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* updateParentComment(identity: string, child_total: number) {}

function* deleteComment(action: ItemLocalAction) {
  const { identity } = action.payload;
  const item = yield* getItem(identity);

  if (!item) return;

  const { apiClient, compactUrl } = yield* getGlobalContext();

  const canDelete = item.extra?.can_delete;

  if (!canDelete) return;

  const config = yield* getItemActionConfig(item, 'deleteItem');

  if (!config.apiUrl) return;

  const ok = yield* handleActionConfirm(config);

  if (!ok) return;

  try {
    const response = yield apiClient.request({
      method: config.apiMethod,
      url: compactUrl(config.apiUrl, item)
    });

    yield* handleActionFeedback(response);
    yield* deleteEntity(identity);

    const data = response.data.data;

    if (data.parent_id) {
      yield* updateParentComment(
        `comment.entities.comment.${data.parent_id}`,
        data.child_total
      );
    } else {
      // update resource feed
      if (data?.feed_id) {
        yield* updateData(`feed.entities.feed.${data.feed_id}`, data);
      }

      // update resource origin
      const id = data.alternative_item_id || data.item_id;
      const resource = data.alternative_item_type || data.item_type;

      if (resource && id) {
        // eslint-disable-next-line max-len
        const resourceIdentity = `${data.item_module_id}.entities.${resource}.${id}`;
        yield* updateData(resourceIdentity, data);
      }
    }
  } catch (error) {
    yield* handleActionError(error);
  }
}

function* updateData(identity: string, data: Record<string, any>) {
  if (!identity) return;

  try {
    const feedItem = yield* getItem(identity);

    if (!feedItem) return;

    const { statistic } = data;
    const { related_comments, statistic: currentStatistic } = feedItem;

    const newRelatedComment = related_comments
      ? related_comments.filter(i => i !== identity)
      : [];
    const newStatistic = { ...currentStatistic, ...statistic };

    yield* patchEntity(identity, {
      related_comments: newRelatedComment,
      statistic: newStatistic
    });
  } catch (err) {}
}

const sagas = [takeEvery('deleteComment', deleteComment)];

export default sagas;
