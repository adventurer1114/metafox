import { BasicFileItem } from '@metafox/framework';
import { isVideoType } from '@metafox/utils';
import { get } from 'lodash';
import { all } from 'redux-saga/effects';

export function uploadSingleFile(
  apiClient: any,
  fileItem: BasicFileItem,
  params: Record<string, any>,
  url: string = '/file'
) {
  const formData = new FormData();
  formData.append(params.name || 'file', fileItem.file);

  Object.keys(params).forEach(name => {
    formData.append(name, params[name]);
  });

  const type = isVideoType(fileItem.file.type) ? 'video' : 'photo';

  formData.append('type', type);
  formData.append('item_type', type);
  formData.append('file_type', type);

  return apiClient
    .request({
      url,
      method: 'post',
      data: formData
    })
    .then(response => get(response, 'data.data'));
}

export function* uploadFiles(
  apiClient: any,
  fileItems: BasicFileItem[],
  params: Record<string, any>,
  url?: string
) {
  return yield all(
    fileItems.map(item => uploadSingleFile(apiClient, item, params, url))
  );
}
