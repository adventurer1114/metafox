/**
 * @type: saga
 * name: saga.userForgotPassword
 */
import {
  FormSubmitAction,
  getGlobalContext,
  handleActionError,
  handleActionFeedback
} from '@metafox/framework';
import { takeLatest } from 'redux-saga/effects';

export function* userForgotPassword({
  payload: { values, method, form, action }
}: FormSubmitAction) {
  const { apiClient, navigate } = yield* getGlobalContext();

  try {
    const response = yield apiClient.request({
      method,
      url: action,
      data: values
    });

    yield* handleActionFeedback(response);
    navigate('/login');
  } catch (error) {
    yield* handleActionError(error, form);
  } finally {
    form.setSubmitting(false);
  }
}

const effects = [takeLatest('user/forgotPassword', userForgotPassword)];

export default effects;
