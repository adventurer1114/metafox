/**
 * @type: saga
 * name: photo.saga.deletePhoto
 */

import {
  ENTITY_REFRESH,
  PAGINATION_REFRESH,
  patchEntity
} from '@metafox/framework';
import { put, takeEvery } from 'redux-saga/effects';

function* deletePhotoDone(action: {
  payload: {
    feed_id: string;
    album_id?: string;
    user?: string;
    is_profile_photo?: boolean;
    is_cover_photo?: boolean;
  };
}) {
  const { feed_id, user, is_profile_photo, is_cover_photo, album_id } =
    action.payload;

  if (is_profile_photo) {
    yield* patchEntity(user, {
      avatar: null,
      avatar_id: null
    });
  }

  if (is_cover_photo) {
    yield* patchEntity(user, {
      cover: null,
      cover_photo_id: null,
      cover_photo_position: null
    });
  }

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

const sagas = [takeEvery('photo/photo/deleteItem/DONE', deletePhotoDone)];

export default sagas;
