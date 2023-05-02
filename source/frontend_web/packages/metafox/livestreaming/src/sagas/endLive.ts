/**
 * @type: saga
 * name: livestreaming.saga.endLive
 */

import {
  getGlobalContext,
  getItem,
  getItemActionConfig,
  ItemLocalAction,
  handleActionFeedback,
  handleActionError,
  handleActionConfirm
} from '@metafox/framework';
import { takeLatest, put } from 'redux-saga/effects';

function* endLive(action: ItemLocalAction) {
  const { id } = action.payload;
  const identity = `livestreaming.entities.live_video.${id}`;
  const item = yield* getItem(identity);

  if (!item || !item?.is_streaming) return;

  const { apiClient, compactUrl } = yield* getGlobalContext();

  const config = yield* getItemActionConfig(item, 'endLive');

  if (!config.apiUrl) return;

  const ok = yield* handleActionConfirm(config);

  if (!ok) return;

  try {
    const response = yield apiClient.request({
      method: config.apiMethod,
      url: compactUrl(config.apiUrl, item)
    });

    if (response) {
      yield put({
        type: 'livestreaming/updateStatusOffline',
        payload: { identity }
      });
    }

    yield* handleActionFeedback(response);
  } catch (error) {
    handleActionError(error);
  }
}

const sagas = [takeLatest('livestreaming/end-live', endLive)];

export default sagas;
