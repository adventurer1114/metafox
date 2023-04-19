/**
 * @type: saga
 * name: user.verifyEmail
 */
import {
  getGlobalContext,
  handleActionFeedback,
  LocalAction
} from '@metafox/framework';
import { get } from 'lodash';
import { takeLatest, put } from 'redux-saga/effects';

function* verifyEmail({ payload }: LocalAction<{ hash: string }>) {
  const { apiClient } = yield* getGlobalContext();

  try {
    const response = yield apiClient.post(`/user/verify/email/${payload.hash}`);

    yield put({
      type: 'user/verifyEmail/update',
      payload: {
        loading: false,
        success: true
      }
    });
    yield* handleActionFeedback(response);
  } catch (err) {
    const error = get(err, 'response.data.error');

    yield put({
      type: 'user/verifyEmail/update',
      payload: { loading: false, error }
    });
  }
}

const sagas = takeLatest('user/verify/email', verifyEmail);

export default sagas;
