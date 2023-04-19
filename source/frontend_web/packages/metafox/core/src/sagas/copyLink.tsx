/**
 * @type: saga
 * name: core.copyLink
 */

import { getGlobalContext, getItem, ItemLocalAction } from '@metafox/framework';
import { takeLatest } from 'redux-saga/effects';

function* copyLink(action: ItemLocalAction) {
  const { copyToClipboard, toastBackend } = yield* getGlobalContext();
  const {
    payload: { identity }
  } = action;
  const item = yield* getItem(identity);

  if (!item?.url) return;

  yield copyToClipboard(item.url);
  toastBackend.success('Copied to clipboard');
}

const sagas = [takeLatest('copyLink', copyLink)];

export default sagas;
