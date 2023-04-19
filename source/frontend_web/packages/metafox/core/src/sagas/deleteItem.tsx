/**
 * @type: saga
 * name: core.deleteItem
 */

import {
  deleteEntity,
  getGlobalContext,
  getItem,
  getItemActionConfig,
  handleActionConfirm,
  handleActionError,
  handleActionFeedback,
  ItemLocalAction,
  makeDirtyPaging
} from '@metafox/framework';
import { put, takeEvery } from 'redux-saga/effects';

function* handleItemDeletedDone<
  T extends { module_name: string; resource_name: string; _identity: string }
>(item: T) {
  const { navigate, getPageParams, dialogBackend } = yield* getGlobalContext();

  const { _pageType, appName, resourceName, identity } = getPageParams();

  if (
    (_pageType === 'viewItem' &&
      appName === item?.module_name &&
      resourceName === item?.resource_name) ||
    (_pageType === 'profile' && identity === item?._identity)
  ) {
    const config = yield* getItemActionConfig(item, 'homePage');
    const pageUrl = config?.pageUrl || '/';

    navigate(pageUrl, { replace: true });
  }

  if (_pageType === 'viewItemInModal') {
    dialogBackend.dismiss(true);
    navigate(-1);
  }
}

export function* deleteItem({ payload }: ItemLocalAction) {
  const { identity } = payload;
  const item = yield* getItem(identity);

  if (!item) return;

  const { apiClient, compactUrl } = yield* getGlobalContext();

  const { module_name, resource_name } = item;

  const config = yield* getItemActionConfig(item, 'deleteItem');

  if (!config?.apiUrl) return;

  const ok = yield* handleActionConfirm(config);

  if (!ok) return false;

  try {
    const response = yield apiClient.request({
      method: config?.apiMethod || 'delete',
      url: compactUrl(config.apiUrl, item)
    });

    yield* handleItemDeletedDone(item);

    yield* handleActionFeedback(response);
    yield* deleteEntity(identity);

    yield put({
      type: `${module_name}/${resource_name}/deleteItem/DONE`,
      payload: { ...payload, ...item, ...response.data.data }
    });

    // reload feed for case delete embed item except feed

    module_name !== 'feed' && (yield makeDirtyPaging('feed'));

    return true;
  } catch (error) {
    yield* handleActionError(error);
  }

  return false;
}

const sagas = [
  takeEvery('deleteItem', deleteItem),
  takeEvery('deleteItemDetail', deleteItem)
];

export default sagas;
