/**
 * @type: saga
 * name: bgstatus.bootstrap
 */
import { getGlobalContext, GlobalState } from '@metafox/framework';
import { put, select, takeLatest } from 'redux-saga/effects';

const getLoadedState = (state: GlobalState) => state.bgstatus.loaded;

export function* bootstrap() {
  const loaded = yield select(getLoadedState);

  if (loaded) return;

  const { apiClient } = yield* getGlobalContext();

  const response = yield apiClient.request({
    url: '/pstatusbg-collection',
    method: 'get',
    params: {}
  });

  const data = response.data?.data;

  if (!data?.length) return;

  yield put({
    type: 'bgstatus/FULFILL',
    payload: {
      collections: data,
      defaultItems: data[0].backgrounds
    }
  });
}

const sagas = [takeLatest('bgstatus/LOAD', bootstrap)];

export default sagas;
