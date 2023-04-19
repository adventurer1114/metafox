/**
 * @type: saga
 * name: updatedVideo
 */

import {
  ENTITY_REFRESH,
  getGlobalContext,
  LocalAction,
  PAGINATION_REFRESH
} from '@metafox/framework';
import { takeEvery, put } from 'redux-saga/effects';

function* updatedVideo({ payload: { id } }: LocalAction<{ id: string }>) {
  const { navigate } = yield* getGlobalContext();
  // FOXSOCIAL5-3424
  // yield* viewItem('video', 'video', id);
  navigate('/video/my');
}

function* deleteVideoDone(action: {
  payload: {
    feed_id: string;
    album_id?: string;
  };
}) {
  const { feed_id, album_id } = action.payload;

  if (album_id) {
    yield put({
      type: PAGINATION_REFRESH,
      payload: {
        apiUrl: `/photo/album/${album_id}`,
        apiParams: {
          view: 'latest'
        },
        pagingId: `photo-album/${album_id}`
      }
    });
  }

  if (feed_id) {
    yield put({
      type: ENTITY_REFRESH,
      payload: { identity: `feed.entities.feed.${feed_id}` }
    });
  }
}

const sagas = [
  takeEvery('@updatedItem/video', updatedVideo),
  takeEvery('video/video/deleteItem/DONE', deleteVideoDone)
];

export default sagas;
