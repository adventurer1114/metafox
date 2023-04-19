/**
 * @type: saga
 * name: subscription_package.saga.paymentItem
 */

import { getItem, ItemLocalAction } from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';
import { openMultiStepForm } from '@metafox/form/sagas';

function* paymentPackage(action: ItemLocalAction) {
  const { identity } = action.payload;
  const item = yield* getItem(identity);

  if (!item) return;

  const { module_name: moduleName, resource_name: resourceName } = item;

  yield* openMultiStepForm({
    identity,
    resourceName,
    moduleName,
    actionName: 'getPaymentPackageForm'
  });
}

const sagas = [takeEvery('subscription/paymentPackage', paymentPackage)];

export default sagas;
