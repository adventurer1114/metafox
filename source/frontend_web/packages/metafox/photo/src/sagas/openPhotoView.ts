/**
 * @type: saga
 * name: photo.saga.openPhotoView
 */

import {
  getGlobalContext,
  getResourceAction,
  LocalAction,
  PAGINATION,
  PAGINATION_REFRESH,
  patchEntity
} from '@metafox/framework';
import { compactUrl } from '@metafox/utils';
import { put, takeLatest } from 'redux-saga/effects';
import { APP_PHOTO, RESOURCE_ALBUM } from '../constant';

type OpenPhotoViewAction = LocalAction<{ id: string }>;

function* openPhotoView(action: OpenPhotoViewAction) {
  const {
    payload: { id }
  } = action;
  const { navigate } = yield* getGlobalContext();
  const pathname = `photo/${id}`;

  navigate({ pathname }, { state: { asModal: true } });
}

export function* loadPhotosSuccess(
  action: LocalAction<{ identity: string }, { ids: string[]; pagingId: string }>
) {
  const {
    payload,
    meta: { ids, pagingId }
  } = action;

  let itemIdentity = payload?.identity;

  if (pagingId.indexOf('album') !== -1) {
    itemIdentity = `photo.entities.photo_album.${pagingId.split('/')[1]}`;
  }

  yield* patchEntity(itemIdentity, { photos: ids });
}

export function* loadPhotoSet(action: LocalAction<{ photo_set: string }>) {
  const {
    payload: { photo_set }
  } = action;
  const { getPageParams } = yield* getGlobalContext();
  const pageParam: any = getPageParams();

  const identity = `photo.entities.photo_set.${photo_set}`;
  const config = yield* getResourceAction(APP_PHOTO, APP_PHOTO, 'viewPhotoSet');

  if (!config) return;

  yield put({
    type: PAGINATION,
    payload: {
      apiUrl: compactUrl(config.apiUrl, { id: photo_set }),
      pagingId: `${pageParam?.module_name}/photo_set/${photo_set}`
    },
    meta: {
      successAction: {
        type: 'photo/photos/LOAD_SUCCESS',
        payload: { identity, photo_set }
      }
    }
  });
}

export function* loadPhotoAlbum(action: LocalAction<{ photo_album: string }>) {
  const {
    payload: { photo_album }
  } = action;
  const identity = `photo.entities.photo_album.${photo_album}`;
  const config = yield* getResourceAction(
    APP_PHOTO,
    RESOURCE_ALBUM,
    'getAlbumItems'
  );

  if (!config) return;

  yield put({
    type: PAGINATION,
    payload: {
      apiUrl: compactUrl(config.apiUrl, { id: photo_album }),
      pagingId: `photo-album/${photo_album}`
    },
    meta: {
      successAction: {
        type: 'photo/photos/LOAD_SUCCESS',
        payload: { identity, photo_album }
      }
    }
  });
}
export function* reLoadPhotoAlbum(
  action: LocalAction<{ photo_album: string }>
) {
  const {
    payload: { photo_album }
  } = action;
  const identity = `photo.entities.photo_album.${photo_album}`;
  const config = yield* getResourceAction(
    APP_PHOTO,
    RESOURCE_ALBUM,
    'getAlbumItems'
  );

  if (!config) return;

  yield put({
    type: PAGINATION_REFRESH,
    payload: {
      apiUrl: compactUrl(config.apiUrl, { id: photo_album }),
      pagingId: `photo-album/${photo_album}`
    },
    meta: {
      successAction: {
        type: 'photo/photos/LOAD_SUCCESS',
        payload: { identity, photo_album }
      }
    }
  });
}

export function* presentSimplePhoto({ payload }: LocalAction<{ src: string }>) {
  const { dialogBackend } = yield* getGlobalContext();

  yield dialogBackend.present({
    component: 'photo.dialog.simplePhoto',
    props: payload
  });
}

export function* presentCreateNewAlbum({
  payload
}: LocalAction<{ id: string }>) {
  const { dialogBackend } = yield* getGlobalContext();

  yield dialogBackend.present({
    component: 'photo.dialog.addPhotoAlbum',
    props: payload
  });
}

const sagas = [
  takeLatest('photo/openPhotoView', openPhotoView),
  takeLatest('photo/photo_set/LOAD', loadPhotoSet),
  takeLatest('photo/photo_album/LOAD', loadPhotoAlbum),
  takeLatest('photo/photo_album/RELOAD', reLoadPhotoAlbum),
  takeLatest('photo/photos/LOAD_SUCCESS', loadPhotosSuccess),
  takeLatest('photo-album/LOAD_SUCCESS', loadPhotosSuccess),
  takeLatest('photo/presentSimplePhoto', presentSimplePhoto),
  takeLatest('photo/presentCreateNewAlbum', presentCreateNewAlbum)
];

export default sagas;
