/**
 * @type: saga
 * name: comment.captchaProcess
 */

import { ItemLocalAction } from '@metafox/framework';
import { takeEvery, put, take } from 'redux-saga/effects';

function* processCaptcha(
  action: ItemLocalAction<{
    settingCaptcha: Record<string, any>;
    onSuccess?: (data: any) => void;
    setLoading?: (data: boolean) => void;
    handleSubmit?: (data: any, clear: boolean) => void;
  }>
) {
  const { settingCaptcha, onSuccess, setLoading, handleSubmit } =
    action.payload;
  const isGoogleCaptchaV3 = settingCaptcha?.default === 'recaptcha_v3';
  const isImageCaptcha = settingCaptcha?.default === 'image_captcha';

  if (isGoogleCaptchaV3) {
    const siteKey = isGoogleCaptchaV3
      ? settingCaptcha?.recaptcha_v3?.site_key
      : null;

    yield put({
      type: 'captcha/recaptcha/token',
      payload: { siteKey, actionSuccess: 'comment/captcha/token/response' }
    });
    const result = yield take('comment/captcha/token/response');

    const token = result.payload;
    setLoading(false);
    handleSubmit({ captchaData: { captcha: token } }, false);
    onSuccess && onSuccess(token);
  }

  if (isImageCaptcha) {
    yield put({
      type: 'captcha/image_captcha/showDialog',
      payload: {
        form_action: 'comment.create_comment',
        actionSuccess: 'comment/captcha/token/response',
        onSubmit: (values, form) => {
          setLoading(false);
          handleSubmit({ captchaData: values, formCaptcha: form }, false);
          onSuccess && onSuccess(values);
        }
      }
    });
    // const result = yield take('comment/captcha/token/response');

    // const captchaData = result.payload;

    // setLoading(false);
    // handleSubmit({ captchaData }, false);
    // onSuccess && onSuccess(captchaData);
  }
}

const sagas = [takeEvery('comment/processCaptcha', processCaptcha)];

export default sagas;
