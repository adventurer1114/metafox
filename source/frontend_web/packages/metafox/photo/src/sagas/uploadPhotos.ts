/**
 * @type: saga
 * name: photo.formSubmit
 */
import { uploadFiles } from '@metafox/core/sagas/utils';
import {
  FormSubmitAction,
  getGlobalContext,
  handleActionError,
  handleActionFeedback,
  makeDirtyPaging
} from '@metafox/framework';
import { isEmpty } from 'lodash';
import { call, put, takeEvery } from 'redux-saga/effects';

export function* uploadMultiPhotos({
  payload: {
    values: data,
    dialog,
    action,
    method,
    form,
    dialogItem,
    pageParams,
    secondAction
  }
}: FormSubmitAction) {
  const { apiClient, dialogBackend, compactUrl } = yield* getGlobalContext();
  const { files, attachments, fileItemType, attachmentItemType, ...mainData } =
    data;
  let tempFiles = [];

  try {
    if (!isEmpty(files)) {
      const uploadResults = yield call(uploadFiles, apiClient, files, {
        item_type: fileItemType
      });

      tempFiles = uploadResults.map(x => ({
        id: x.temp_file,
        type: x.item_type || 'photo'
      }));
    }

    if (mainData?.add_new_album === 0) {
      delete mainData.new_album;
    }

    const response = yield apiClient.request({
      method,
      url: compactUrl(action, pageParams),
      data: { files: tempFiles, ...mainData }
    });

    if (response.data?.data) {
      const { resource_name } = response.data?.data;

      yield put({
        type: secondAction || `@updatedItem/${resource_name}`,
        payload: { ...data, ...response.data?.data },
        meta: {}
      });
    }

    yield* handleActionFeedback(response, form);

    ['post', 'POST'].includes(method) &&
      (yield* makeDirtyPaging(compactUrl(action, pageParams)));

    if (dialog) {
      if (dialogItem) {
        dialogItem.setDialogValue(response.data?.data);
      } else {
        dialogBackend.dismiss();
      }
    }
  } catch (error) {
    yield* handleActionError(error, form);
  } finally {
    form.setSubmitting(false);
  }
}

const sagas = [takeEvery('@photo/uploadMultiPhotos/submit', uploadMultiPhotos)];

export default sagas;
