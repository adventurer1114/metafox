/**
 * @type: saga
 * name: group.saga.getMember
 */

import { getResourceAction } from '@metafox/framework';
import { put, takeLatest } from 'redux-saga/effects';
import { APP_GROUP, RESOURCE_GROUP } from '@metafox/group';

function* getMember() {
  const dataSource = yield* getResourceAction(
    APP_GROUP,
    RESOURCE_GROUP,
    'getForMentionMembers'
  );

  yield put({
    type: 'group/saga/pickerMember/response',
    payload: dataSource
  });
}

export default takeLatest('group/saga/pickerMember/get', getMember);
