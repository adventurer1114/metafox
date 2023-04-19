/**
 * @deprecated
 * @type: saga
 * name: user.logout
 */
import { getGlobalContext } from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

/**
 * Deprecated by cause redirect two slow and User see flash screen
 * @returns
 */
export function* logoutSaga() {
  const { redirectTo, apiClient, cookieBackend, getSetting } =
    yield* getGlobalContext();

  const token = yield cookieBackend.get('token');
  const redirect_after_logout = getSetting('user.redirect_after_logout');

  if (!token) return;

  yield cookieBackend.remove('token');
  yield cookieBackend.remove('refreshToken');
  yield cookieBackend.remove('dateExpiredToken');

  // TO DO send request to notify logged out.
  try {
    yield apiClient.request({
      url: '/logout',
      method: 'POST',
      data: {},
      headers: {
        Authorization: `Bearer ${token}`
      }
    });
  } catch (err) {
    // do nothing
  }

  const admincp = process.env.MFOX_BUILD_TYPE === 'admincp';
  const baseUrl = process.env.PUBLIC_URL;

  const redirectUrl = admincp ? `${baseUrl}/admincp` : baseUrl;

  redirectTo(redirect_after_logout || redirectUrl || '/');
}

const sagaEffect = takeEvery('@logout', logoutSaga);

export default sagaEffect;
