/**
 * @type: saga
 * name: marketplace_invoice.saga.changeItem
 */

import {
  getItem,
  ItemLocalAction,
  getItemActionConfig,
  getGlobalContext,
  handleActionError,
  handleActionConfirm,
  handleActionFeedback,
  PAGINATION_REFRESH
} from '@metafox/framework';
import { takeEvery, put } from 'redux-saga/effects';

function* changeInvoice({
  payload
}: ItemLocalAction<{ pagingId?: string; identity?: string }>) {
  const { identity, pagingId } = payload;
  const item = yield* getItem(identity);

  if (!item) return;

  const { apiClient, compactUrl, compactData } = yield* getGlobalContext();

  const config = yield* getItemActionConfig(item, 'changeItem');

  if (!config?.apiUrl) return;

  const ok = yield* handleActionConfirm(config);

  if (!ok) return false;

  try {
    const response = yield apiClient.request({
      method: config?.apiMethod,
      url: compactUrl(config.apiUrl, item),
      data: compactData(config.apiParams, item)
    });

    if (response?.data && pagingId) {
      const status = response.data?.status;

      if (status === 'success') {
        yield put({
          type: PAGINATION_REFRESH,
          payload: {
            pagingId
          }
        });
      }
    }

    yield* handleActionFeedback(response);

    return true;
  } catch (error) {
    yield* handleActionError(error);
  }

  return false;
}

const sagas = [takeEvery('marketplace/changeItem', changeInvoice)];

export default sagas;
