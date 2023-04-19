/**
 * @type: saga
 * name: saga.giftPoints
 */

import { APP_ACTIVITY } from '@metafox/activity-point';
import {
  getGlobalContext,
  getItemActionConfig,
  handleActionError
} from '@metafox/framework';
import { takeLatest } from 'redux-saga/effects';

export function* giftPoints(action) {
  const { identity } = action.payload;

  if (!identity) return;

  const id = identity.split('.')[3];

  const { dialogBackend } = yield* getGlobalContext();

  const dataSource = yield* getItemActionConfig(
    { resource_name: APP_ACTIVITY, module_name: APP_ACTIVITY },
    'getGiftForm'
  );

  try {
    yield dialogBackend.present({
      component: 'core.dialog.RemoteForm',
      props: {
        dataSource,
        pageParams: { id }
      }
    });
  } catch (error) {
    yield* handleActionError(error);
  }
}

const effects = [takeLatest('getGiftForm', giftPoints)];

export default effects;
