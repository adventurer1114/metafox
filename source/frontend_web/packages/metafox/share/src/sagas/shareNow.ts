/**
 * @type: saga
 * name: saga.shareNow
 */

import { fetchDetailSaga } from '@metafox/core/sagas/fetchDetailSaga';
import { getTotalPins } from '@metafox/feed/sagas/getTotalPins';
import {
  fetchDetail,
  getGlobalContext,
  getItem,
  getSession,
  handleActionError,
  handleActionFeedback,
  LocalAction,
  PAGINATION_PUSH_INDEX,
  patchEntity
} from '@metafox/framework';
import { getSharingItemPrivacy } from '@metafox/user/sagas';
import { fetchDataPrivacy } from '@metafox/user/sagas/sharingItemPrivacy';
import { get, isString } from 'lodash';
import { put, takeLatest, call } from 'redux-saga/effects';
import { APP_SAVED } from '../constants';

export function* shareNow(
  action: LocalAction<{ identity: string; parentIdentity: string }>
) {
  const {
    payload: { identity }
  } = action;

  const item = yield* getItem(identity);
  const { apiClient } = yield* getGlobalContext();
  const { user } = yield* getSession();

  yield call(fetchDataPrivacy, {
    payload: { id: user.id }
  });
  const privacy = yield* getSharingItemPrivacy('share');

  if (!item) return;

  let embed_item = null;

  if (
    item.embed_object &&
    isString(item.embed_object) &&
    (item.embed_object.startsWith('feed') || item.resource_name === APP_SAVED)
  ) {
    embed_item = yield* getItem(item.embed_object);
  }

  const item_id = embed_item ? embed_item.id : item.id;
  const item_type = embed_item
    ? embed_item.resource_name
    : item.resource_name || item.item_type;

  try {
    const response = yield apiClient.request({
      url: '/feed/share',
      method: 'post',
      data: {
        item_type,
        item_id,
        privacy: privacy?.value,
        post_type: 'wall',
        post_content: ''
      }
    });

    const ok = 'success' === get(response, 'data.status');
    const feedId = get(response, 'data.data.ids');

    if (!ok || !feedId) return;

    const apiUrl = `feed/${feedId}?item_type=${user?.resource_name}&item_id=${user?.id}`;

    const data = yield* fetchDetailSaga(fetchDetail(apiUrl));

    if (item_type === 'feed') {
      const identityFeed = `feed.entities.feed.${item_id}`;
      const dataFeed = yield* getItem(identityFeed);

      if (dataFeed) {
        const newData = {
          statistic: {
            ...dataFeed?.statistic,
            total_share: (dataFeed?.statistic?.total_share || 0) + 1
          }
        };
        yield* patchEntity(identityFeed, newData);
      }
    }

    const totalPinsHome = yield* getTotalPins(null);
    const totalPinsProfile = yield* getTotalPins(parseInt(user?.id));
    const indexId = [totalPinsProfile, totalPinsHome];

    yield put({
      type: PAGINATION_PUSH_INDEX,
      payload: {
        data,
        pagingId: [`/feed?user_id=${user?.id}`, '/feed'],
        indexId
      }
    });

    yield* handleActionFeedback(response);
  } catch (error) {
    yield* handleActionError(error);
  }
}

const sagas = [takeLatest('shareNow', shareNow)];

export default sagas;
