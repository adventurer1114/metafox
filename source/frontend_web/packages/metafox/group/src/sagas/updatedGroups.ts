/**
 * @type: saga
 * name: updateGroups
 */

import {
  LocalAction,
  viewItem,
  PAGINATION_CLEAR,
  getGlobalContext
} from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* updateGroups({ payload: { id } }: LocalAction<{ id: string }>) {
  const { dispatch } = yield* getGlobalContext();

  yield* viewItem('group', 'group', id);

  dispatch({
    type: PAGINATION_CLEAR,
    payload: { pagingId: '/user/shortcut' }
  });
}

const sagas = [takeEvery('@updatedItem/group', updateGroups)];

export default sagas;
