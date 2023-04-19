/**
 * @type: saga
 * name: updatedPhoto
 */

import { LocalAction, viewItem } from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* updatedPhoto({ payload: { id } }: LocalAction<{ id: string }>) {
  yield* viewItem('photo', 'photo', id);
}

const sagas = [takeEvery('@updatedItem/photo', updatedPhoto)];

export default sagas;
