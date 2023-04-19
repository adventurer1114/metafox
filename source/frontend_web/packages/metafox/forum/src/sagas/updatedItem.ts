/**
 * @type: saga
 * name: forum_thread.saga.updatedItem
 */

import { LocalAction, viewItem } from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* updatedItem({ payload: { id } }: LocalAction<{ id: string }>) {
  yield* viewItem('forum', 'forum_thread', id);
}

const sagas = [takeEvery('@updatedItem/forum_thread', updatedItem)];

export default sagas;
