/**
 * @type: saga
 * name: feed.removeTaggedFriend
 */

import {
  fulfillEntity,
  getGlobalContext,
  getItem,
  getItemActionConfig,
  handleActionError,
  handleActionFeedback,
  ItemLocalAction,
  patchEntity
} from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* removeTaggedFriend({ payload }: ItemLocalAction) {
  const { identity } = payload;
  const item = yield* getItem(identity);

  if (!item) return;

  const { apiClient, compactUrl, normalization } = yield* getGlobalContext();

  const { total_friends_tagged, tagged_friends } = item;

  const config = yield* getItemActionConfig(item, 'removeTaggedFriend');

  if (!config?.apiUrl) return;

  try {
    const response = yield apiClient.request({
      method: config?.apiMethod || 'delete',
      url: compactUrl(config.apiUrl, item)
    });

    const result = yield normalization.normalize(response?.data?.data);

    yield* fulfillEntity(result.data);

    yield* handleActionFeedback(response);
  } catch (error) {
    yield* patchEntity(identity, {
      total_friends_tagged,
      tagged_friends
    });
    yield* handleActionError(error);
  }
}

const sagas = [
  takeEvery('removeTaggedFriend', removeTaggedFriend),
  takeEvery('removeTaggedFriendDetail', removeTaggedFriend)
];

export default sagas;
