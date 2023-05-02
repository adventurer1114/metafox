/**
 * @type: saga
 * name: core.formSubmit
 */
import { getFormValues } from '@metafox/form/sagas';
import {
  BasicFileItem,
  FormSubmitAction,
  FORM_SUBMIT,
  getGlobalContext,
  handleActionError,
  handleActionFeedback,
  makeDirtyPaging,
  PAGINATION_RESET_ALL
} from '@metafox/framework';
import { collectFileItemDotProps } from '@metafox/utils';
import { cloneDeep, get, isFunction, set, isArray } from 'lodash';
import { all, call, put, race, takeLatest } from 'redux-saga/effects';

const handleSuccess = (cb?: () => void, ...data) => {
  if (isFunction(cb)) cb(...data);
};

const handleFailure = (cb?: () => void, error?: any) => {
  if (isFunction(cb)) cb(error);
};

/**
 * Execute upload get error result or id of uploaded file
 * - result.error: describe error message
 * - result.id: identity of uploaded item in database
 * - other from response.data
 *
 * @param {ApiClient} apiClient - axios instance
 * @param {BasicFileItem} fileItem -
 * @returns { error?:string, id?: number}
 *
 */
export function uploadFile(
  apiClient: any,
  fileItem: BasicFileItem
): { error?: string; uid: string; id: number } {
  const formData = new FormData();
  let params: Record<string, any> = {};
  formData.append('file', fileItem.file);
  params = {
    name: 'file',
    type: fileItem.file_type,
    item_type: fileItem.fileItemType,
    file_type: fileItem.file_type,
    file_name: fileItem.file_name,
    file_size: fileItem.file_size,
    thumbnail_sizes: fileItem?.thumbnail_sizes,
    storage_id: fileItem?.storage_id ?? null
  };

  // attached to file
  Object.keys(params).forEach(name => {
    if (!params[name]) return;

    if (isArray(params[name])) {
      const arr = params[name];

      for (let i = 0; i < arr.length; i++) {
        formData.append(`${name}[]`, arr[i]);
      }
    } else {
      formData.append(name, params[name]);
    }
  });

  return apiClient
    .request({
      url: fileItem.upload_url,
      method: 'post',
      data: formData
    })
    .then(response => get(response, 'data.data') || get(response, 'data'))
    .catch(error => ({
      error:
        get(error, 'response.data.errors.file.0') ||
        get(error, 'response.data.error')
    }));
}

class UploadFileError extends Error {
  response = {};

  setResponse(response: any) {
    this.response = response;
  }
}

/**
 *
 * @param {FormSubmitAction} submitAction - submit action here
 * @returns
 */
function* postFormSubmitAttachments(submitAction: FormSubmitAction) {
  const { apiClient } = yield* getGlobalContext();

  const values = cloneDeep(submitAction.payload.values);

  const dotProps = collectFileItemDotProps(values);

  // there are no file to upload
  if (!dotProps.length) return;

  const files = dotProps.map<BasicFileItem>(name => get(values, name));

  if (!files.length) return;

  // call yield all does make failed over.
  // should improve failover
  // a file error should not stop other process
  const response = yield all(
    files.map(file => call(uploadFile, apiClient, file))
  );

  const errors = {};

  // updated collection data
  response.forEach((res, index) => {
    // just uploaded successful or null
    const path = dotProps[index];

    if (res.error) {
      set(errors, path, res.error);
    } else {
      const currentValue = get(values, path);
      set(values, path, { ...currentValue, ...res });
    }
  });

  submitAction.payload.values = values;

  // error occur through uploads
  if (Object.keys(errors).length > 0) {
    const _err = new UploadFileError();
    _err.setResponse({ data: { errors } });
    throw _err;
  }
}

export function* formSubmitSaga(submitAction: FormSubmitAction) {
  const isCaptchaImage =
    submitAction?.payload?.formSchema?.captcha?.captcha_type ===
    'image_captcha';
  let preventFinally = false;

  try {
    yield call(postFormSubmitAttachments, submitAction);

    const { apiClient, dialogBackend, compactUrl } = yield* getGlobalContext();

    const {
      payload: {
        values: data,
        dialog,
        action,
        method,
        form,
        dialogItem,
        pageParams,
        secondAction,
        successAction,
        enctype,
        preventReset,
        keepPaginationData
      },
      meta
    } = submitAction;

    if (meta?.onSubmitting) {
      yield call(meta.onSubmitting);
    }

    const formValues = yield* getFormValues(submitAction);

    if (!formValues) {
      form.setSubmitting(false);

      return;
    }

    if (enctype === 'multipart/form-data') {
      const formData = new FormData();

      Object.keys(data).forEach(name => {
        if (name !== 'file') {
          formData.append(name, data[name]);
        }
      });

      formData.append('file', data.file.file);

      const response = yield apiClient.request({
        method,
        url: compactUrl(action, pageParams),
        data: formData
      });

      yield* handleActionFeedback(response, form);

      if (successAction) {
        yield put({
          type: successAction,
          payload: { ...data, ...response.data?.data }
        });

        yield race([`${successAction}/DONE`, `${successAction}/FULFILL`]);
      }

      if (dialog) {
        if (dialogItem) {
          dialogItem.setDialogValue(response.data?.data);
        } else {
          dialogBackend.dismiss();
        }
      }

      handleSuccess(meta?.onSuccess, data);

      return;
    }

    const response = yield apiClient.request({
      method,
      url: compactUrl(action, pageParams),
      data: formValues
    });

    if (response.data?.access_token) {
      yield put({
        type: secondAction,
        payload: response.data
      });
    }

    if (response.data?.data) {
      const { resource_name } = response.data?.data;

      yield put({
        type: secondAction || `@updatedItem/${resource_name}`,
        payload: { ...data, ...response.data?.data },
        meta: {}
      });
    }

    form.setSubmitting(false);

    yield* handleActionFeedback(response, form);

    if (['post', 'POST'].includes(method) && !keepPaginationData) {
      yield* makeDirtyPaging(compactUrl(action, pageParams));
    }

    if (successAction) {
      yield put({
        type: successAction,
        payload: { ...data, ...response.data?.data }
      });

      yield race([`${successAction}/DONE`, `${successAction}/FULFILL`]);
    }

    const continueAction = response?.data?.meta?.continueAction;

    if (continueAction) {
      const { type, payload } = continueAction;

      yield put({
        type,
        payload: { ...payload, values: data, responseData: response.data?.data }
      });
    }

    if (dialog) {
      if (dialogItem) {
        dialogItem.setDialogValue(response.data?.data);
      } else {
        dialogBackend.dismiss();
      }
    }

    handleSuccess(meta?.onSuccess, data);
    !preventReset && put({ type: PAGINATION_RESET_ALL });

    if (
      form?.callbackOnSuccess &&
      typeof form?.callbackOnSuccess === 'function'
    ) {
      form?.callbackOnSuccess();
    }
  } catch (error) {
    const {
      meta,
      payload: { failureAction, form }
    } = submitAction;

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

    if (failureAction) {
      yield put({ type: failureAction });
      yield race([`${failureAction}/DONE`, `${failureAction}/FULFILL`]);
    }

    handleFailure(meta?.onFailure, error);

    yield* handleActionError(error, form);
  } finally {
    // eslint-disable-next-line no-unsafe-finally
    if (preventFinally) return false;

    const { form } = submitAction.payload;
    form.setSubmitting(false);

    if (isCaptchaImage) {
      yield put({
        type: 'captcha_image/validation/end'
      });
    }
  }
}

const sagas = takeLatest(FORM_SUBMIT, formSubmitSaga);

export default sagas;
