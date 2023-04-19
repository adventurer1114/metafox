/**
 * @type: saga
 * name: activitypoint.saga.managePoint
 */

import { openMultiStepForm } from '@metafox/form/sagas';
import { getItem, ItemLocalAction } from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* purchase(action: ItemLocalAction) {
  const { identity } = action.payload;
  const item = yield* getItem(identity);

  if (!item) return;

  const { module_name: moduleName, resource_name: resourceName } = item;

  yield* openMultiStepForm({
    identity,
    resourceName,
    moduleName,
    actionName: 'purchaseItem'
  });
}

const sagas = [takeEvery('activityPoint/purchase', purchase)];

export default sagas;
