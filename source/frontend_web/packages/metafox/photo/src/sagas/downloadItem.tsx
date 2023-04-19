/**
 * @type: saga
 * name: photo.saga.downloadItem
 */
import {
  getGlobalContext,
  getItem,
  getItemActionConfig,
  handleActionConfirm,
  handleActionError,
  ItemLocalAction
} from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function download(url, token) {
  let filename = '';
  const headers = {
    Authorization: `Bearer ${token}`
  };

  fetch(url, { headers })
    .then(response => {
      filename = response.headers
        .get('Content-Disposition')
        .split('filename=')[1];

      return response.blob();
    })
    .then(blob => {
      const link = document.createElement('a');
      link.href = URL.createObjectURL(blob);
      link.download = filename;
      link.click();
    })
    .catch(error => console.log(error));
}

function* downloadItem(action: ItemLocalAction) {
  const { identity } = action.payload;
  const item = yield* getItem(identity);

  if (!item) return;

  const { getConfig, compactUrl, cookieBackend } = yield* getGlobalContext();

  const config = yield* getItemActionConfig(item, 'downloadItem');

  if (!config?.apiUrl) return false;

  const ok = yield* handleActionConfirm(config);

  if (!ok) return false;

  try {
    const url = compactUrl(config.apiUrl, item);

    const token = cookieBackend.get('token');

    const MFOX_API_URL = getConfig('root.env.MFOX_API_URL') || '/api/v1';

    download(`${MFOX_API_URL}${url}`, token);
  } catch (error) {
    yield* handleActionError(error);
  }
}

const sagas = [takeEvery('photo/downloadItem', downloadItem)];

export default sagas;
