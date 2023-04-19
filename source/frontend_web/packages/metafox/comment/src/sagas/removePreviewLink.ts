/**
 * @type: saga
 * name: comment.removePreviewLink
 */

import {
  getGlobalContext,
  getItem,
  handleActionError,
  patchEntity,
  getItemActionConfig
} from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

export function* removePreviewLink(action) {
  const { identity } = action.payload;
  const item = yield* getItem(identity);

  const { apiClient, compactUrl } = yield* getGlobalContext();

  try {
    yield* patchEntity(identity, {
      extra_data: [],
      is_hide: true
    });

    const config = yield* getItemActionConfig(item, 'editItem');

    yield apiClient.request({
      method: config.apiMethod,
      url: compactUrl(config.apiUrl, item),
      data: {
        is_hide: 1
      }
    });
  } catch (error) {
    yield* handleActionError(error);
  }
}

const sagas = [takeEvery('comment/removePreviewLink', removePreviewLink)];

export default sagas;
