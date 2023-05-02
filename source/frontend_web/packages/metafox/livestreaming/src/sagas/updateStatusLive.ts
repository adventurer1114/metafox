/**
 * @type: saga
 * name: livestreaming.saga.updateStatus
 */

import { ItemLocalAction, patchEntity } from '@metafox/framework';
import { takeLatest } from 'redux-saga/effects';

function* updateStatus(action: ItemLocalAction) {
  const { identity } = action.payload;

  try {
    yield* patchEntity(identity, { is_streaming: false, _live_watching: true });
  } catch (error) {}
}

const sagas = [takeLatest('livestreaming/updateStatusOffline', updateStatus)];

export default sagas;
