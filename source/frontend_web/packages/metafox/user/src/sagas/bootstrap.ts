/**
 * @type: saga
 * name: user.bootstrap
 */

/**
 * @type: saga
 * name: saga.coreSetting
 */
import {
  APP_BOOTSTRAP,
  AuthUserShape,
  getGlobalContext,
  LOGGED_OUT,
  LS_ACCOUNT_NAME,
  RELOAD_USER,
  CACHE_SETTING_KEY,
  fulfillEntity,
  REFRESH_TOKEN
} from '@metafox/framework';
import { uniqBy } from 'lodash';
import { put, takeLatest } from 'redux-saga/effects';

// allow cache settings

export function* verifyToken() {
  const { cookieBackend, localStore } = yield* getGlobalContext();

  // valid token
  try {
    const token = cookieBackend.get('token');

    if (!token) {
      return false;
    }
  } catch (error) {
    localStore.remove(CACHE_SETTING_KEY);
  }

  return false;
}

function* loadUser() {
  const { apiClient, localStore, cookieBackend, normalization } =
    yield* getGlobalContext();

  const response = yield apiClient.get('/me');

  const user = response.data?.data;

  if (!user?.id) {
    try {
      const refreshToken = cookieBackend.get('refreshToken');

      if (refreshToken) {
        yield put({ type: REFRESH_TOKEN });
      } else {
        // clean token if not logged in
        cookieBackend.remove('token');
        /// UAT: Sometimes, it will display Guest as a registered account when load to home page
        yield put({ type: LOGGED_OUT });
      }
    } catch (error) {
      console.log(error);
    }
  } else {
    // override redux session state
    yield put({ type: 'session/update', payload: user });

    // update user store
    try {
      const result = normalization.normalize({ ...user });
      yield* fulfillEntity(result.data);
    } catch (error) {
      console.log(error);
    }

    // yield* fulfillEntity(result.data);

    let accounts = localStore.getJSON<AuthUserShape[]>(LS_ACCOUNT_NAME) || [];
    // save to account
    accounts.unshift(user);

    accounts = uniqBy(accounts, 'id');

    yield localStore.set(LS_ACCOUNT_NAME, JSON.stringify(accounts));
  }
}

function* bootstrapUser() {
  try {
    yield* loadUser();
    yield put({ type: '@bootstrap/user/DONE' });
  } catch (error) {
    yield put({ type: '@bootstrap/user/DONE', error: true });
  }
}

function* reloadUser() {
  yield* loadUser();
  yield put({ type: `${RELOAD_USER}/DONE` });
}

const sagas = [
  takeLatest(APP_BOOTSTRAP, bootstrapUser),
  takeLatest(RELOAD_USER, reloadUser)
];

export default sagas;
