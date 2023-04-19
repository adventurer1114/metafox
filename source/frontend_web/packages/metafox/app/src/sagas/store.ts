/**
 * @type: saga
 * name: install
 */

import {
  getGlobalContext,
  getItem,
  handleActionError,
  handleActionFeedback,
  LocalAction,
  patchEntity
} from '@metafox/framework';
import { call, takeEvery } from 'redux-saga/effects';

function* onInstall(action: LocalAction<{ identity }>) {
  const { apiClient } = yield* getGlobalContext();

  const { identity } = action.payload;

  const item = yield* getItem(identity);

  try {
    yield* patchEntity(identity, {
      is_installing: true,
      installation_status: 'installing'
    });
    const response = yield call(
      apiClient.post,
      '/admincp/app/store/product/install',
      {
        name: item.identity,
        app_version: item.version
      }
    );

    yield* handleActionFeedback(response);
    yield* patchEntity(identity, {
      is_installing: false,
      installation_status: 'processing'
    });
  } catch (err) {
    yield* handleActionError(err);
    yield* patchEntity(identity, {
      is_installing: false,
      installation_status: 'installed'
    });
  }
}

const sagas = [takeEvery('app/store/install', onInstall)];

export default sagas;
