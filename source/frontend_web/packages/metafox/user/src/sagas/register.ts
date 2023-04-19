/**
 * @type: saga
 * name: user/register
 */
import { getFormValues } from '@metafox/form/sagas';
import {
  FormSubmitAction,
  getGlobalContext,
  handleActionError,
  handleActionFeedback
} from '@metafox/framework';
import { ACTION_LOGIN_BY_TOKEN } from '@metafox/user';
import { get } from 'lodash';
import { put, takeLatest } from 'redux-saga/effects';
import { isExternalLink } from '@metafox/utils';

export function* register(submitAction: FormSubmitAction) {
  const { apiClient, navigate, getSetting, redirectTo } =
    yield* getGlobalContext();

  const {
    payload: { form, method, action }
  } = submitAction;

  const values = yield* getFormValues(submitAction);

  try {
    const response = yield apiClient.request({
      method,
      url: action,
      data: values,
      headers: {
        Authorization: undefined
      }
    });

    // try logged in access if token is given
    const token = get(response, 'data.token');

    if (token) {
      yield put({
        type: ACTION_LOGIN_BY_TOKEN,
        payload: {
          token,
          returnUrl: '/',
          remember: true
        }
      });
    }

    const id = get(response, 'data.data.id');
    const isVerifyEmail = get(response, 'data.data.email_verified_at');
    const redirect_after_signup = getSetting('user.redirect_after_signup');

    yield* handleActionFeedback(response);

    if (!isVerifyEmail) {
      navigate('/resend-email');

      return;
    }

    if (id) {
      if (isExternalLink(redirect_after_signup)) {
        redirectTo(redirect_after_signup);

        return;
      }

      navigate(redirect_after_signup || '/login', { replace: true });
    }
  } catch (error) {
    yield* handleActionError(error, form);
  } finally {
    form.setSubmitting(false);
  }
}

const effects = [takeLatest('user/register', register)];

export default effects;
