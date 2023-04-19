/**
 * @type: saga
 * name: chat.saga.composer
 */

import {
  BasicFileItem,
  getGlobalContext,
  handleActionError,
  LocalAction
} from '@metafox/framework';
import { collectFileItemDotProps, getFileExtension } from '@metafox/utils';
import { cloneDeep, get, set, uniqueId } from 'lodash';
import { takeEvery, call, all, put } from 'redux-saga/effects';
import { ReactMode } from '../types';

export interface AttachmentItemProps {}

export function uploadFile(
  apiClient: any,
  fileItem: BasicFileItem & { index?: number },
  onUploadProgress: any
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
    file_size: fileItem.file_size
  };

  // attached to file
  Object.keys(params).forEach(name => {
    formData.append(name, params[name]);
  });

  return apiClient
    .post(fileItem.upload_url, formData, {
      onUploadProgress: e => onUploadProgress(e, fileItem.index)
    })
    .then(response => get(response, 'data.data') || get(response, 'data'))
    .catch(error => ({
      error:
        get(error, 'response.data.errors.file.0') ||
        get(error, 'response.data.error')
    }));
}

function* handleSubmit(
  action: LocalAction<
    {
      msgId?: string;
      rid: string;
      text: string;
      sticker_id?: string;
      reactMode?: ReactMode;
      attachments?: any[];
    },
    {
      onFailure?: (msg: string) => void;
      onSuccess?: () => void;
    }
  >
) {
  const {
    sticker_id,
    msgId,
    rid,
    text,
    reactMode = 'no_react',
    attachments = undefined
  } = action.payload;

  try {
    const { apiClient } = yield* getGlobalContext();

    const msg = sticker_id
      ? String(sticker_id)
      : String(text).replace(/@\[.+\]\((here|all)\)/g, '@$1');
    const reply_id = reactMode === 'reply' ? msgId : undefined;

    if (action?.meta?.onSuccess) yield action?.meta?.onSuccess();

    const result = yield apiClient.request({
      method: reactMode === 'edit' ? 'PUT' : 'POST',
      url: reactMode === 'edit' ? `/chat/${msgId}` : '/chat',
      data: { reply_id, room_id: rid, message: msg, attachments }
    });

    if (!result) return false;

    return true;
  } catch (error: any) {
    if (action?.meta?.onFailure) yield action?.meta?.onFailure(error.message);

    yield* handleActionError(error);
  }
}

function* postFormSubmitAttachments(data: any, onUploadProgress: any) {
  const { apiClient } = yield* getGlobalContext();

  // there are no file to upload
  if (!data.length) return;

  const values = cloneDeep(data);

  const dotProps = collectFileItemDotProps(values);

  // there are no file to upload
  if (!dotProps.length) return;

  const files = dotProps.map<BasicFileItem>(name => get(values, name));

  if (!files.length) return;

  // call yield all does make failed over.
  // should improve failover
  // a file error should not stop other process
  const response = yield all(
    files.map((file, index) =>
      call(uploadFile, apiClient, { ...file, index }, onUploadProgress)
    )
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

  // error occur through uploads
  if (Object.keys(errors).length > 0) {
    return { errors };
  }

  return values;
}

function* handleUpload(
  action: LocalAction<
    {
      files: any;
      rid: string;
      text: string;
      reactMode?: ReactMode;
      msgId?: string;
    },
    {
      onFailure?: (msg: string) => void;
      onSuccess?: () => void;
    }
  >
) {
  const { onSuccess, onFailure } = action.meta;
  const { files, msgId, rid, text, reactMode } = action.payload;

  const key = Date.now();

  const { dialogBackend, i18n, dispatch } = yield* getGlobalContext();

  if (!files && !files.length) return;

  try {
    yield put({
      type: 'chat/room/addRoomFileProgress',
      payload: {
        rid,
        key,
        value: {
          count: files.length,
          files
        }
      }
    });

    let fileItems: AttachmentItemProps[] = [];

    for (const index of Object.keys(files)) {
      const itemResult: AttachmentItemProps = {
        id: 0,
        status: 'create',
        upload_url: '/attachment',
        download_url: URL.createObjectURL(files[index]),
        source: URL.createObjectURL(files[index]),
        file: files[index],
        file_name: files[index].name,
        file_size: files[index].size,
        file_type: files[index].type,
        uid: uniqueId('file'),
        fileItemType: 'chat_message',
        extension: getFileExtension(files[index].name)
      };

      // const fileItemSize = itemResult.item.file.size;
      // const fileItemName = itemResult.item.file_name;

      // if (fileItemSize > max_upload_filesize && max_upload_filesize !== 0) {
      //   dialogBackend.alert({
      //     message: i18n.formatMessage(
      //       { id: 'warning_upload_limit_one_file' },
      //       {
      //         fileName: shortenFileName(fileItemName, 30),
      //         fileSize: parseFileSize(itemResult.item.file.size),
      //         maxSize: parseFileSize(max_upload_filesize)
      //       }
      //     )
      //   });

      //   break;
      // }

      fileItems.push(itemResult);
    }
    let progressObj = {};

    const onUploadProgress = (event, name) => {
      const progress = Math.round((event.loaded * 100) / event.total) || 0;
      progressObj = { ...progressObj, [name]: progress };

      const totalProgress = Object.keys(progressObj).reduce(
        (accumulator, currentValue) =>
          accumulator + progressObj[currentValue] / fileItems.length,
        0
      );

      dispatch({
        type: 'chat/room/updateRoomFileProgress',
        payload: {
          rid,
          key,
          progress: totalProgress
        }
      });
      // eslint-disable-next-line no-console
    };

    fileItems = yield call(
      postFormSubmitAttachments,
      fileItems,
      onUploadProgress
    );

    const attachments: any = fileItems ? fileItems : undefined;

    if (attachments?.errors) {
      const message = Object.keys(attachments?.errors).map(
        err => attachments?.errors[err]
      );

      yield dialogBackend.alert({
        title: i18n.formatMessage({ id: 'oops' }),
        message: message[0]
      });

      dispatch({
        type: 'chat/room/deleteRoomFileProgress',
        payload: {
          rid,
          key
        }
      });

      if (onFailure) yield onFailure('error');

      return;
    }

    const result = yield* handleSubmit({
      payload: { msgId, rid, text, reactMode, attachments }
    } as any);

    if (!result) return;

    dispatch({
      type: 'chat/room/deleteRoomFileProgress',
      payload: {
        rid,
        key
      }
    });

    if (onSuccess) yield onSuccess();
  } catch (error: any) {
    if (onFailure) yield onFailure(error.message);

    yield* handleActionError(error);
  }
}

const sagas = [
  takeEvery('chat/composer/SUBMIT', handleSubmit),
  takeEvery('chat/composer/upload', handleUpload)
];

export default sagas;
