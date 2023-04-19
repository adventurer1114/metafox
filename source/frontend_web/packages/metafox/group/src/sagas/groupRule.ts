/**
 * @type: saga
 * name: saga.group.rule
 */

import {
  getGlobalContext,
  getItem,
  getItemActionConfig,
  handleActionError,
  handleActionFeedback,
  ItemLocalAction,
  PAGINATION_REFRESH,
  patchEntity
} from '@metafox/framework';
import { compactData, compactUrl } from '@metafox/utils';
import qs from 'querystring';
import { put, takeEvery } from 'redux-saga/effects';
import { APP_GROUP } from '../constant';

export function* addGroupRuleForm(action: ItemLocalAction) {
  const {
    payload: { identity }
  } = action;
  const item = yield* getItem(identity);
  const { dialogBackend } = yield* getGlobalContext();

  if (!item) return;

  const { apiUrl, apiParams } = yield* getItemActionConfig(
    { resource_name: 'group_rule', module_name: 'group' },
    'addItem'
  );

  yield dialogBackend.present({
    component: 'group.manager.dialog.rule',
    props: {
      dataSource: {
        apiUrl,
        apiParams: compactData(apiParams, { id: item.id })
      }
    }
  });
}

export function* updateGroupRuleFrom(action: ItemLocalAction) {
  const {
    payload: { identity }
  } = action;
  const item = yield* getItem(identity);
  const { dialogBackend } = yield* getGlobalContext();

  if (!item) return;

  const { apiUrl } = yield* getItemActionConfig(
    { resource_name: 'group_rule', module_name: 'group' },
    'editItem'
  );

  yield dialogBackend.present({
    component: 'group.manager.dialog.rule',
    props: {
      id: item.id,
      dataSource: {
        apiUrl: compactUrl(apiUrl, { id: item.id })
      }
    }
  });
}

export function changeRuleGroupDialog(action) {
  const { payload } = action;
  const { values, form } = payload;

  if (values.rule_example) {
    form.setFieldValue('description', values.rule_example.description, false);
    form.setFieldValue('title', values.rule_example.title, false);
    form.setFieldValue('rule_example', undefined, false);
  }
}

function* reloadGroupRule(action) {
  const { payload } = action;
  const { group_id } = payload;

  const { apiUrl, apiParams = 'group_id=:id' } = yield* getItemActionConfig(
    { resource_name: 'group_rule', module_name: 'group' },
    'viewAll'
  );

  const pagingId = `${apiUrl}?${qs.stringify(
    compactData(apiParams, { id: group_id })
  )}`;

  yield put({
    type: PAGINATION_REFRESH,
    payload: {
      apiUrl,
      apiParams,
      pagingId
    }
  });
}

function* updateRuleConfirmation(
  action: ItemLocalAction & {
    payload: { identity: string; is_rule_confirmation: boolean };
  } & { meta: { onSuccess: () => void; onFailure: () => {} } }
) {
  const {
    payload: { identity, is_rule_confirmation },
    meta: { onSuccess, onFailure }
  } = action;
  const { apiClient } = yield* getGlobalContext();

  try {
    const dataSource = yield* getItemActionConfig(
      { module_name: APP_GROUP, resource_name: APP_GROUP },
      'updateRuleConfirmation'
    );

    const { apiUrl, apiMethod, apiParams } = dataSource;

    const id = identity.split('.')[3];

    const response = yield apiClient.request({
      method: apiMethod,
      url: apiUrl,
      params: compactData(apiParams, {
        id,
        is_rule_confirmation: +is_rule_confirmation
      })
    });

    yield* patchEntity(identity, response?.data.data);

    yield* handleActionFeedback(response);
    typeof onSuccess === 'function' && onSuccess();
  } catch (error) {
    yield* handleActionError(error);
    typeof onFailure === 'function' && onFailure();
  }
}

const sagas = [
  takeEvery('changeRuleGroupDialog', changeRuleGroupDialog),
  takeEvery('updateGroupRule', updateGroupRuleFrom),
  takeEvery('addGroupRule', addGroupRuleForm),
  takeEvery('group/updateRuleConfirmation', updateRuleConfirmation),
  takeEvery('@updatedItem/group_rule', reloadGroupRule)
];

export default sagas;
