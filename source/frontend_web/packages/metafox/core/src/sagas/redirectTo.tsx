/**
 * @type: saga
 * name: core.redirectTo
 */

import { getGlobalContext, ItemLocalAction } from '@metafox/framework';
import { compactUrl } from '@metafox/utils';
import { takeLatest } from 'redux-saga/effects';

function* redirectTo(action: ItemLocalAction<{ url: string }>) {
  const {
    payload: { url, ...others }
  } = action;
  const { getPageParams } = yield* getGlobalContext();
  const pageParams = getPageParams();

  url && window.location.replace(compactUrl(url, { ...pageParams, ...others }));

  yield;
}

const sagas = [takeLatest('@redirectTo', redirectTo)];

export default sagas;
