/**
 * @type: saga
 * name: saga.user.manageHidden
 */

import {
  getGlobalContext,
  getItem,
  getItemActionConfig,
  handleActionError,
  handleActionFeedback,
  ItemLocalAction,
  patchEntity
} from '@metafox/framework';
import { takeLatest } from 'redux-saga/effects';

function* manageHidden() {
  const { dialogBackend } = yield* getGlobalContext();

  yield dialogBackend.present({
    component: 'user.dialog.manageHidden',
    props: {}
  });
}

function* unhideItem({
  payload: { identity },
  meta
}: ItemLocalAction<any, any>) {
  const { apiClient, compactUrl } = yield* getGlobalContext();
  const item = yield* getItem(identity);
  const config = yield* getItemActionConfig(item, 'unhideItem');
  const user = yield* getItem(item.user);

  try {
    yield* patchEntity(identity, { unHide: 1 });

    const res = yield apiClient.request({
      method: config?.apiMethod,
      url: compactUrl(config?.apiUrl, user)
    });

    yield* handleActionFeedback(res);
  } catch (error) {
    yield* patchEntity(identity, { unHide: 0 });
    yield* handleActionError(error);
  }
}

function* undoUnhideItem({
  payload: { identity },
  meta
}: ItemLocalAction<any, any>) {
  const { apiClient, compactUrl } = yield* getGlobalContext();
  const item = yield* getItem(identity);
  const config = yield* getItemActionConfig(item, 'undoUnhideItem');
  const user = yield* getItem(item.user);

  try {
    yield* patchEntity(identity, { unHide: 0 });
    const res = yield apiClient.request({
      method: config?.apiMethod,
      url: compactUrl(config?.apiUrl, user)
    });
    yield* handleActionFeedback(res);
  } catch (error) {
    yield* patchEntity(identity, { unHide: 1 });
    yield* handleActionError(error);
  }
}

const sagas = [
  takeLatest('manageHidden', manageHidden),
  takeLatest('unhideItem', unhideItem),
  takeLatest('undoUnhideItem', undoUnhideItem)
];

export default sagas;
