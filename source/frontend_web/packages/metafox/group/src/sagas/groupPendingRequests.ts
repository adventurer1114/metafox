/**
 * @type: saga
 * name: saga.group.pendingRequest
 */

import {
  deleteEntity,
  getGlobalContext,
  getItem,
  getItemActionConfig,
  handleActionError,
  handleActionFeedback,
  ItemLocalAction
} from '@metafox/framework';
import { compactData } from '@metafox/utils';
import { takeEvery } from 'redux-saga/effects';
import { APP_GROUP, GROUP_REQUEST } from '..';

function* declinePendingRequest(action: ItemLocalAction) {
  const {
    payload: { identity }
  } = action;
  const { apiClient } = yield* getGlobalContext();

  const dataSource = yield* getItemActionConfig(
    { module_name: APP_GROUP, resource_name: GROUP_REQUEST },
    'denyMemberRequest'
  );

  const { apiUrl, apiMethod, apiParams } = dataSource;

  if (!identity) return null;

  const item = yield* getItem(identity);

  if (!item) return null;

  try {
    const { group_id, user } = item;
    const userId = user.split('.')[3];

    const response = yield apiClient.request({
      method: apiMethod,
      url: apiUrl,
      params: compactData(apiParams, { user_id: userId, group_id })
    });

    yield* deleteEntity(identity);

    yield* handleActionFeedback(response);
  } catch (error) {
    yield* handleActionError(error);
  }
}

function* approvePendingRequest(
  action: ItemLocalAction & {
    payload: Record<string, any>;
  } & { meta: { onSuccess: () => void; onFailure: () => {} } }
) {
  const {
    payload: { identity }
  } = action;

  if (!identity) return null;

  const item = yield* getItem(identity);

  if (!item) return null;

  const { apiClient } = yield* getGlobalContext();
  const { apiUrl, apiMethod, apiParams } = yield* getItemActionConfig(
    { module_name: APP_GROUP, resource_name: GROUP_REQUEST },
    'acceptMemberRequest'
  );

  try {
    const { group_id, user } = item;
    const userId = user.split('.')[3];

    const response = yield apiClient.request({
      method: apiMethod,
      url: apiUrl,
      params: compactData(apiParams, { user_id: userId, group_id })
    });

    yield* deleteEntity(identity);

    yield* handleActionFeedback(response);
  } catch (error) {
    yield* handleActionError(error);
  }
}

const sagas = [
  takeEvery('group/declinePendingRequest', declinePendingRequest),
  takeEvery('group/approvePendingRequest', approvePendingRequest)
];

export default sagas;
