/**
 * @type: saga
 * name: sticker.saga.sticker
 */

import { fulfillEntity, getGlobalContext } from '@metafox/framework';
import { put, select, takeLatest } from 'redux-saga/effects';
import { getMyStickerSet } from '../selectors';
import { AppState } from '../types';

export function* openStickerPickerDialog() {
  const { loaded }: AppState['myStickerSet'] = yield select(getMyStickerSet);

  if (loaded) return;

  const { apiClient, normalization } = yield* getGlobalContext();

  const response = yield apiClient.request({
    method: 'GET',
    url: '/sticker-set',
    params: {
      user_id: 1
    }
  });

  const responseData = response.data?.data;
  const result = normalization.normalize(responseData);

  yield* fulfillEntity(result.data);

  yield put({
    type: 'sticker/myStickerSet/FULFILL',
    payload: { data: result.ids }
  });
}

const sagas = [
  takeLatest('sticker/openStickerPickerDialog', openStickerPickerDialog)
];

export default sagas;
