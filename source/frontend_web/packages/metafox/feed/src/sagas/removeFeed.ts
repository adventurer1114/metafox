/**
 * @type: saga
 * name: core.removeFeed
 */

import {
  deleteEntity,
  getGlobalContext,
  getItem,
  getItemActionConfig,
  handleActionConfirm,
  handleActionError,
  handleActionFeedback,
  ItemLocalAction
} from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* removeFeed({ payload }: ItemLocalAction) {
  const { identity } = payload;
  const item = yield* getItem(identity);

  if (!item) return;

  const { apiClient, compactUrl } = yield* getGlobalContext();

  const config = yield* getItemActionConfig(item, 'removeItem');

  if (!config?.apiUrl) return;

  const ok = yield* handleActionConfirm(config);

  if (!ok) return false;

  try {
    const response = yield apiClient.request({
      method: config?.apiMethod,
      url: compactUrl(config.apiUrl, item)
    });
    yield* deleteEntity(identity);
    yield* handleActionFeedback(response);
  } catch (error) {
    yield* handleActionError(error);
  }

  return false;
}

const sagas = [takeEvery('feed/removeItem', removeFeed)];

export default sagas;
