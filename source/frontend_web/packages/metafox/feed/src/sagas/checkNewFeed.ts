/**
 * @type: saga
 * name: saga.feed.checkNewFeed
 */

import {
  getGlobalContext,
  getItemActionConfig,
  ItemLocalAction,
  getItem
} from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';
import { APP_FEED } from '@metafox/feed';
import { getTotalPins } from './getTotalPins';

function* checkNews(
  action: ItemLocalAction & {
    payload: Record<string, any>;
  } & { meta: { onSuccess: () => void; onFailure: () => {} } }
) {
  const {
    payload: { pagingId },
    meta
  } = action;

  const { apiClient, compactData } = yield* getGlobalContext();
  const { apiUrl, apiMethod, apiParams } = yield* getItemActionConfig(
    { module_name: APP_FEED, resource_name: APP_FEED },
    'checkNew'
  );
  const pagingItem = yield* getItem(`pagination.${pagingId}`);
  const totalPins = yield* getTotalPins(null);

  if (!pagingItem?.ids || !pagingItem?.ids?.length || !apiUrl) return;

  try {
    const last_feed_id = pagingItem?.ids[totalPins || 0]?.split('.')[3];
    const last_pin_feed_id =
      totalPins > 0 ? pagingItem?.ids[0]?.split('.')[3] : undefined;

    const response = yield apiClient.request({
      method: apiMethod,
      url: apiUrl,
      params: compactData(apiParams, { last_feed_id, last_pin_feed_id })
    });

    if (!response?.data) return;

    const { data } = response.data;

    if (data?.reload && meta?.onSuccess) {
      meta?.onSuccess();
    }
  } catch (error) {
    console.log(error);
  }
}

const sagas = [takeEvery('feed/checkNews', checkNews)];

export default sagas;
