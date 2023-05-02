/**
 * @type: saga
 * name: user.login
 */
import { getFormValues } from '@metafox/form/sagas';
import {
  APP_BOOTSTRAP,
  AuthUserShape,
  FormSubmitAction,
  getGlobalContext,
  getSessionSelector,
  handleActionError,
  handleActionFeedback,
  LocalAction,
  LS_ACCOUNT_NAME,
  LS_GUEST_NAME,
  REFRESH_TOKEN,
  LOGGED_OUT,
  STRATEGY_REFRESH_TOKEN,
  ACTION_UPDATE_TOKEN,
  getResourceAction
} from '@metafox/framework';
import { ACTION_LOGIN_BY_TOKEN } from '@metafox/user';
import { get, isArray, uniqBy } from 'lodash';
import { put, select, takeEvery, takeLatest } from 'redux-saga/effects';
import { MULTI_FACTOR_AUTH_TOKEN } from '../constant';

export function* loginSaga(submitAction: FormSubmitAction) {
  const {
    type,
    payload: { action, form, formSchema }
  } = submitAction;

  const {
    cookieBackend,
    apiClient,
    location,
    navigate,
    localStore,
    redirectTo
  } = yield* getGlobalContext();

  const values = yield* getFormValues(submitAction);

  if (!values) {
    form.setSubmitting(false);

    return;
  }

  const {
    email: username,
    password,
    remember,
    captcha,
    returnUrl,
    image_captcha_key
  } = values;

  let preventFinally = false;
  const isCaptchaImage = formSchema?.captcha?.captcha_type === 'image_captcha';

  try {
    cookieBackend.remove('token');
    cookieBackend.remove('refreshToken');
    cookieBackend.remove('dateExpiredToken');
    const response = yield apiClient.request({
      url: action,
      method: 'POST',
      data: {
        image_captcha_key,
        captcha,
        username,
        password
      },
      headers: {
        Authorization: ''
      }
    });

    yield* handleActionFeedback(response, form);

    const mfa_token = get(response, 'data.data.mfa_token');
    const token = get(response, 'data.access_token');
    const refreshToken = get(response, 'data.refresh_token');
    const expiresIn = get(response, 'data.expires_in');
    const error = get(response, 'data.error_description');

    const config = yield* getResourceAction('mfa', 'user_auth', 'authForm');

    if (mfa_token) {
      if (!config?.apiUrl) return;

      localStore.set(MULTI_FACTOR_AUTH_TOKEN, mfa_token);

      navigate('/authenticator');

      return;
    }

    if (token) {
      yield put({
        type: ACTION_LOGIN_BY_TOKEN,
        payload: {
          token,
          refreshToken,
          expiresIn,
          returnUrl,
          remember,
          keepUrl: type === '@loginFromDialog'
        }
      });
    } else {
      if (form) form.setErrors({ email: error });
    }
  } catch (error) {
    if (isCaptchaImage) {
      const isError = get(error, 'response.data.errors.captcha');

      yield put({
        type: 'captcha_image/validation/end',
        payload: isError ? error : undefined
      });

      if (isError) {
        preventFinally = true;
        form.setSubmitting(false);
        form.submitForm();

        return;
      }
    }

    if (type !== '@loginFromDialog') {
      if (!['/', '/login'].includes(location.pathname)) {
        const pathName = encodeURIComponent(
          location.pathname + location.search
        );
        redirectTo(`/login?returnUrl=${pathName}`);
      }
    }

    yield* handleActionError(error, form);
  } finally {
    // eslint-disable-next-line no-unsafe-finally
    if (preventFinally) return false;

    // Do something finally
    if (form) {
      form.setSubmitting(false);
    }

    if (isCaptchaImage) {
      yield put({
        type: 'captcha_image/validation/end'
      });
    }
  }
}

function* viewAsGuest() {
  const { localStore } = yield* getGlobalContext();

  if (localStore.get(LS_GUEST_NAME)) {
    localStore.remove(LS_GUEST_NAME);
  } else {
    localStore.set(LS_GUEST_NAME, '1');
  }
}

function* bootstrap() {
  const { localStore } = yield* getGlobalContext();

  const data = localStore.getJSON<AuthUserShape[]>(LS_ACCOUNT_NAME);

  if (!data) {
    return [];
  }

  yield put({
    type: 'session/setAccounts',
    payload: data.filter(x => x.full_name)
  });
}

function* removeAccount({ payload }: LocalAction<number>) {
  const { accounts } = yield select(getSessionSelector);

  if (!accounts) return;

  // eslint-disable-next-line eqeqeq
  const newValue = accounts.filter(x => x.id != payload);

  yield* saveAccountToLocalStore(newValue);

  yield put({
    type: 'session/setAccounts',
    payload: newValue
  });
}

function* saveAccountToLocalStore(accounts: AuthUserShape[]) {
  const { localStore } = yield* getGlobalContext();

  accounts = uniqBy(accounts, 'id');

  // keep permanent

  yield localStore.set(LS_ACCOUNT_NAME, JSON.stringify(accounts));
}

function* addAccount({ payload }: LocalAction<Required<{ id: number }>>) {
  const { accounts } = yield select(getSessionSelector);

  if (!isArray(accounts) || !payload.id) return;

  accounts.push(payload);

  yield* saveAccountToLocalStore(accounts);

  yield put({
    type: 'session/setAccounts',
    payload: accounts
  });
}

function* addMoreAccount() {
  const { dialogBackend, i18n } = yield* getGlobalContext();

  yield dialogBackend.present({
    component: 'user.dialog.LoginDialog',
    props: {
      title: i18n.formatMessage({ id: 'add_existing_account' }),
      formName: 'add_account_popup'
    }
  });
}

function* verifyEmail() {
  const { navigate } = yield* getGlobalContext();

  navigate('/resend-email');
}

let refreshingToken = false;

export function* refreshToken({ payload }: LocalAction<{ reload: boolean }>) {
  const { cookieBackend, apiClient } = yield* getGlobalContext();
  const { reload = true } = payload || {};

  if (refreshingToken) return;

  refreshingToken = true;
  try {
    const refresh_token = cookieBackend.get('refreshToken');

    if (refresh_token) {
      const response = yield apiClient.request({
        url: 'user/refresh',
        method: 'POST',
        data: {
          refresh_token
        },
        headers: {
          Authorization: ''
        }
      });

      yield* handleActionFeedback(response);

      const token = get(response, 'data.access_token');
      const refreshToken = get(response, 'data.refresh_token');
      const expiresIn = get(response, 'data.expires_in');

      if (token) {
        yield put({
          type: ACTION_UPDATE_TOKEN,
          payload: {
            token,
            refreshToken,
            expiresIn
          }
        });

        if (reload) {
          window.location.reload();
        } else {
          yield put({ type: STRATEGY_REFRESH_TOKEN });
        }
      }
    } else {
      // clean token if not logged in
      cookieBackend.remove('token');
      cookieBackend.remove('refreshToken');
      cookieBackend.remove('dateExpiredToken');
      /// UAT: Sometimes, it will display Guest as a registered account when load to home page
      yield put({ type: LOGGED_OUT });
    }

    refreshingToken = false;
  } catch (error) {
    cookieBackend.remove('token');
    cookieBackend.remove('refreshToken');
    cookieBackend.remove('dateExpiredToken');
    window.location.reload();
  }
}

export function* loginAuthentication({
  payload
}: LocalAction<{ access_token: any; expires_in: any; refresh_token: string }>) {
  const { localStore } = yield* getGlobalContext();

  if (!payload) return;

  const { access_token, expires_in, refresh_token } = payload;

  if (access_token) {
    yield put({
      type: ACTION_LOGIN_BY_TOKEN,
      payload: {
        token: access_token,
        refreshToken: refresh_token,
        expiresIn: expires_in,
        remember: false,
        returnUrl: '/'
      }
    });
    yield localStore.remove(MULTI_FACTOR_AUTH_TOKEN);
  }
}

export function* callbackLogin({
  payload
}: LocalAction<{ accessToken: string; provider: string }>) {
  const { accessToken, provider } = payload;
  const { apiClient, compactUrl } = yield* getGlobalContext();

  const config = yield* getResourceAction(
    'socialite',
    'socialite_auth',
    'callback'
  );

  try {
    const response = yield apiClient.request({
      method: config.apiMethod,
      url: compactUrl(config.apiUrl, { provider }),
      params: { token: accessToken }
    });

    const token = get(response, 'data.data.access_token');

    if (token) {
      yield put({
        type: ACTION_LOGIN_BY_TOKEN,
        payload: {
          token,
          returnUrl: '/'
        }
      });
    }
  } catch (err) {
    yield* handleActionError(err);
  }
}

const sagaEffect = [
  takeLatest(['@login', '@loginFromDialog'], loginSaga),
  takeLatest('user/addMoreAccount', addMoreAccount),
  takeLatest('session/addAccount', addAccount),
  takeLatest('session/viewAsGuest', viewAsGuest),
  takeLatest('user/removeAccount', removeAccount),
  takeEvery(APP_BOOTSTRAP, bootstrap),
  takeEvery(REFRESH_TOKEN, refreshToken),
  takeEvery('verify', verifyEmail),
  takeEvery('@loginAuthentication', loginAuthentication),
  takeEvery('login/callback', callbackLogin)
];

export default sagaEffect;
