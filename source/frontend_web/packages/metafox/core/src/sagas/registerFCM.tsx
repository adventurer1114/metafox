/**
 * @type: saga
 * name: core.registerFCM
 */
import {
  getGlobalContext,
  ItemLocalAction,
  getResourceAction,
  getSession
} from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* registerFCM(action: ItemLocalAction<{ token: string }>) {
  const { token } = action.payload;
  const { user: authUser } = yield* getSession();

  const { apiClient, compactData, cookieBackend } = yield* getGlobalContext();

  const config = yield* getResourceAction(
    'authorization',
    'user_device',
    'addItem'
  );

  if (!config?.apiUrl) return false;

  try {
    const response = yield apiClient.request({
      url: config?.apiUrl,
      method: config?.apiMethod || 'post',
      params: compactData(config?.apiParams, {
        token_source: 'firebase',
        device_token: token
      })
    });
    const { status } = response.data || {};

    if (status === 'success') {
      const isRemember = cookieBackend.get('refreshToken');

      cookieBackend.set('fcm-notification', authUser?.id, {
        expires: isRemember ? 30 : undefined
      });
    }
  } catch (err) {
    console.log(err);
  }
}

const sagas = [takeEvery('@registerFCM', registerFCM)];

export default sagas;
