/**
 * @type: saga
 * name: sticker.saga.sticker
 */

import {
  fulfillEntity,
  getGlobalContext,
  getResourceAction,
  ItemLocalAction,
  getItemActionConfig,
  getItem,
  handleActionFeedback,
  handleActionError,
  patchEntity
} from '@metafox/framework';
import { put, select, takeLatest } from 'redux-saga/effects';
import { STICKER, STICKER_SET } from '@metafox/sticker/constant';
import { getMyStickerSet, getAllStickerSet } from '../selectors';
import { AppState } from '../types';

export function* openStickerPickerDialog() {
  const { loaded }: AppState['myStickerSet'] = yield select(getMyStickerSet);

  if (loaded) return;

  const { apiClient, normalization } = yield* getGlobalContext();

  const configMySet = yield* getResourceAction(
    STICKER,
    STICKER_SET,
    'viewMyStickerSet'
  );

  const configRecent = yield* getResourceAction(
    STICKER,
    STICKER,
    'viewRecentSticker'
  );

  const responseRecent = yield apiClient.get(configRecent.apiUrl);

  const responseMySet = yield apiClient.request({
    method: 'GET',
    url: configMySet.apiUrl,
    params: configMySet.apiParams
  });

  const recentSticker = responseRecent.data?.data;
  const myStickerSet = responseMySet.data?.data;
  const resultRecent = normalization.normalize(recentSticker);
  const resultMySet = normalization.normalize(myStickerSet);

  yield* fulfillEntity(resultMySet.data);
  yield* fulfillEntity(resultRecent.data);

  yield put({
    type: 'sticker/myStickerRecent/FULFILL',
    payload: { data: resultRecent.ids }
  });

  yield put({
    type: 'sticker/myStickerSet/FULFILL',
    payload: { data: resultMySet.ids }
  });
}

export function* openDialogSticker() {
  
  const { dialogBackend } = yield* getGlobalContext();

  yield dialogBackend.present({
    component: 'dialog.sticker.manager',
    props: {
      itemView: 'sticker.ui.stickerSet'
    }
  });
}

export function* fetchAllStickerSet() {
  const { loaded }: AppState['stickerSet'] = yield select(getAllStickerSet);

  if (loaded) return;

  const { apiClient, normalization } = yield* getGlobalContext();
  const config = yield* getResourceAction(STICKER, STICKER_SET, 'viewAll');

  const response = yield apiClient.request({
    method: 'GET',
    url: config.apiUrl,
    params: config.apiParams
  });

  const data = response.data?.data;
  const result = normalization.normalize(data);
  yield* fulfillEntity(result.data);

  yield put({
    type: 'sticker/stickerSet/FULFILL',
    payload: { data: result.ids }
  });
}

export function* addToMyList(action: ItemLocalAction) {

  const {
    payload: { identity }
  } = action;
  const item = yield* getItem(identity);
  const { apiClient, compactData } = yield* getGlobalContext();

  const config = yield* getItemActionConfig(item, 'addToMyList');

  try {
    const { apiMethod, apiUrl, apiParams } = config;

    const response = yield apiClient.request({
      method: apiMethod,
      url: apiUrl,
      data: compactData(apiParams, item)
    });
    yield* patchEntity(identity, { is_added: true });

    yield put({
      type: 'sticker/myStickerSet/addItem',
      payload: { data: identity }
    });
    yield* handleActionFeedback(response);
  } catch (error) {
    yield* handleActionError(error);
  }
}

export function* removeFromMyList(action: ItemLocalAction) {

  const {
    payload: { identity }
  } = action;
  const item = yield* getItem(identity);

  const config = yield* getItemActionConfig(item, 'removeFromMyList');
  const { apiClient, compactUrl } = yield* getGlobalContext();

  try {
    const { apiMethod, apiUrl } = config;

    const response = yield apiClient.request({
      method: apiMethod || 'DELETE',
      url: compactUrl(apiUrl, item)
    });

    yield put({
      type: 'sticker/myStickerSet/removeItem',
      payload: { data: identity }
    });

    yield* patchEntity(identity, { is_added: false });
    yield* handleActionFeedback(response);
  } catch (error) {
    yield* handleActionError(error);
  }

}

const sagas = [
  takeLatest('sticker/openStickerPickerDialog', openStickerPickerDialog),
  takeLatest('sticker/openDialogSticker', openDialogSticker),
  takeLatest('sticker/fetchAllStickerSet', fetchAllStickerSet),
  takeLatest('sticker/addStickerSet', addToMyList),
  takeLatest('sticker/removeStickerSet', removeFromMyList)
];

export default sagas;
