/**
 * @type: saga
 * name: saga.coreSetting
 */
import {
  APP_BOOTSTRAP,
  APP_BOOTSTRAP_DONE,
  CACHE_SETTING_KEY,
  getGlobalContext,
  IS_ADMINCP,
  USE_BOOTSTRAP_CACHE as CACHED
} from '@metafox/framework';
import { ACTION_LOAD_SETTING } from '@metafox/framework/state/constants';
import { get } from 'lodash';
import { all, put, take, takeLatest } from 'redux-saga/effects';

function* bootstrapSettings() {
  const { apiClient, setAcl, setAssets, setSetting, localStore } =
    yield* getGlobalContext();

  try {
    let data = CACHED ? localStore.getJSON(CACHE_SETTING_KEY) : undefined;

    const revision = CACHED ? get(data, 'revision', 'now') : 'now';

    const apiUrl = `core/${IS_ADMINCP ? 'admin' : 'web'}/settings/${revision}`;
    const response = yield apiClient.get(apiUrl);

    const keepCached = get(response, 'data.data.keepCached', false);

    if (!keepCached) {
      data = response.data.data;
      localStore.set(CACHE_SETTING_KEY, JSON.stringify(data));
    }

    // verify token me is valid or logout immediately
    yield put({ type: ACTION_LOAD_SETTING, payload: data });

    setAcl(data.acl);
    setSetting(data.settings);
    setAssets(data.assets);

    yield put({ type: '@bootstrap/settings/DONE' });
  } catch (error) {
    yield put({ type: '@bootstrap/settings/DONE', error: true });
  }
}

export function* bootstrap() {
  const results = yield all([
    take('@bootstrap/intl/DONE'),
    take('@bootstrap/user/DONE'),
    take('@bootstrap/theme/DONE'),
    take('@bootstrap/settings/DONE')
  ]);

  const error = results.find(action => action.error);

  yield put({
    type: APP_BOOTSTRAP_DONE,
    payload: { loaded: true, error }
  });
}

const sagas = [
  takeLatest(APP_BOOTSTRAP, bootstrap),
  takeLatest(APP_BOOTSTRAP, bootstrapSettings)
];

export default sagas;
