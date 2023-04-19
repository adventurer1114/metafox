/**
 * @type: saga
 * name: updatedEvent
 */

import { LocalAction, viewItem } from '@metafox/framework';
import { takeEvery, put } from 'redux-saga/effects';

function* updatedEvent({ payload: { id } }: LocalAction<{ id: string }>) {
  yield* viewItem('event', 'event', id);
}

function* eventActive({ payload }) {
  yield put({ type: 'event/active', payload });
}

const sagas = [
  takeEvery('@updatedItem/event', updatedEvent),
  takeEvery('event/hover', eventActive)
];

export default sagas;
