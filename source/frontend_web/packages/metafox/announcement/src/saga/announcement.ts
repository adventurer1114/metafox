/**
 * @type: saga
 * name: announcement.getAnnouncementList
 */

import {
  deleteEntity,
  fulfillEntity,
  getGlobalContext,
  getItem,
  getResourceAction,
  handleActionError,
  patchEntity,
  ItemLocalAction,
  getItemActionConfig
} from '@metafox/framework';
import { takeLatest } from 'redux-saga/effects';

function* getAnnouncementList(action: {
  type: string;
  meta: { onSuccess?: (data: any) => {} };
}) {
  const { apiClient, normalization } = yield* getGlobalContext();

  const { onSuccess } = action.meta;

  try {
    const response = yield apiClient.request({
      url: '/announcement'
    });

    const data = response?.data?.data;
    const meta = response?.data?.meta;
    const result = normalization.normalize(data);

    yield* fulfillEntity(result.data);

    typeof onSuccess === 'function' && onSuccess({ data, meta });
  } catch (error) {
    yield* handleActionError(error);
  }
}

function* getAnnouncementPage(action: { type: string; payload }) {
  const { apiClient, normalization } = yield* getGlobalContext();

  const url = action.payload;

  if (!url) return;

  try {
    const response = yield apiClient.request({
      url
    });

    const data = response?.data?.data;
    const result = normalization.normalize(data);

    yield* fulfillEntity(result.data);
  } catch (error) {
    yield* handleActionError(error);
  }
}

export function* markAsRead(action: {
  type: string;
  payload;
  meta: { onSuccess: () => {} };
}) {
  const { id, isDetail } = action.payload;
  const { onSuccess } = action?.meta;

  const identity = `announcement.entities.announcement.${id}`;

  try {
    const item = yield* getItem(identity);

    if (!item) return null;

    const { module_name, resource_name, statistic } = item;
    const { apiUrl, apiMethod } = yield* getResourceAction(
      module_name,
      resource_name,
      'markAsRead'
    );

    const { apiClient } = yield* getGlobalContext();

    yield apiClient.request({
      method: apiMethod || 'POST',
      url: apiUrl,
      data: { announcement_id: item.id }
    });

    if (item.can_be_closed && !isDetail) {
      yield* deleteEntity(identity);
      typeof onSuccess === 'function' && onSuccess();
    } else {
      yield* patchEntity(identity, {
        is_read: true,
        statistic: {
          ...statistic,
          total_view: statistic?.total_view + 1
        }
      });
    }
  } catch (err) {
    yield* handleActionError(err);
  }
}

export function* openListViewer({ payload }: ItemLocalAction) {
  const { identity } = payload;

  const item = yield* getItem(identity);

  if (!item) return;

  const { dialogBackend, compactData } = yield* getGlobalContext();

  const dataSource = yield* getItemActionConfig(item, 'viewAnalytic');

  try {
    yield dialogBackend.present({
      component: 'announcement.dialog.listViewer',
      props: {
        dialogTitle: 'read_by',
        apiUrl: dataSource.apiUrl,
        apiParams: compactData(dataSource.apiParams, item),
        pagingId: `announcement/openListViewer${item.id}`
      }
    });
  } catch (error) {
    yield* handleActionError(error);
  }
}

const sagas = [
  takeLatest('announcement/getAnnouncementList', getAnnouncementList),
  takeLatest('announcement/getAnnouncementPage', getAnnouncementPage),
  takeLatest('announcement/markAsRead', markAsRead),
  takeLatest('announcement/openListViewer', openListViewer)
];

export default sagas;
