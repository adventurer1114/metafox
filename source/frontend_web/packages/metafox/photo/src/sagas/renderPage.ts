/**
 * @type: saga
 * name: photo.beforeRender
 */

import { fetchDetail, LocalAction } from '@metafox/framework';
import { put, takeLatest } from 'redux-saga/effects';

function* beforeRenderPagePhotoView(
  action: LocalAction<{ photo_id: string; photo_set: string }>
) {
  const { photo_set, photo_id } = action.payload;

  if (photo_set) {
    yield put({
      type: 'photo/photo_set/LOAD',
      payload: {
        photo_set
      }
    });
  }

  if (photo_id) {
    yield put(fetchDetail('/photo/:id', { id: photo_id }));
  }
}

const sagas = [takeLatest('renderPage/photo.view', beforeRenderPagePhotoView)];

export default sagas;
