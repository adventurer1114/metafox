/**
 * @type: saga
 * name: saga.resendEmail
 */
import {
  FormSubmitAction,
  getGlobalContext,
  handleActionError,
  handleActionFeedback
} from '@metafox/framework';
import { takeLatest } from 'redux-saga/effects';

export function* verifyResendEmail({
  payload: { values, method, form, action }
}: FormSubmitAction) {
  const { apiClient } = yield* getGlobalContext();

  try {
    const response = yield apiClient.request({
      method,
      url: action,
      data: values
    });

    yield* handleActionFeedback(response);
  } catch (error) {
    yield* handleActionError(error, form);
  } finally {
    form.setSubmitting(false);
  }
}

const effects = [takeLatest('@user/verify/resendEmail', verifyResendEmail)];

export default effects;
