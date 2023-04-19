/**
 * @type: saga
 * name: marketplace_invoice.saga.paymentItem
 */

import { getItem, ItemLocalAction } from '@metafox/framework';
import { openMultiStepForm } from '@metafox/form/sagas';
import { takeEvery } from 'redux-saga/effects';

function* paymentPackage({ type, payload }: ItemLocalAction) {
  const { identity } = payload;
  const item = yield* getItem(identity);
  const actionName = type.replace('marketplace/', '');

  if (!item) return;

  const { module_name: moduleName, resource_name: resourceName } = item;

  yield* openMultiStepForm({
    identity,
    resourceName,
    moduleName,
    actionName
  });
}

const sagas = [takeEvery(['marketplace/getRepaymentForm'], paymentPackage)];

export default sagas;
