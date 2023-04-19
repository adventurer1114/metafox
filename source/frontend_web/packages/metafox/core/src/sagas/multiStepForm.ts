/**
 * @type: saga
 * name: saga.multiStepForm
 */

import {
  getGlobalContext,
  getItem,
  getItemActionConfig,
  handleActionError,
  ItemLocalAction
} from '@metafox/framework';
import { takeEvery, put } from 'redux-saga/effects';

function* openMultiStepForm(
  action: ItemLocalAction & {
    payload: {
      identity: string;
      resourceName: string;
      moduleName: string;
      actionName?: string;
    };
  }
) {
  try {
    const {
      identity,
      resourceName,
      moduleName,
      actionName = 'getPaymentItem'
    } = action.payload;

    const item = yield* getItem(identity);

    const { dialogBackend, dispatch } = yield* getGlobalContext();
    const dataSource = yield* getItemActionConfig(
      { resource_name: resourceName, module_name: moduleName },
      actionName
    );

    yield dialogBackend.present({
      component: 'core.dialog.RemoteForm',
      props: {
        dataSource,
        pageParams: { id: item.id },
        onLoaded: ({ data, meta }) => {
          if (!data || !meta) return;

          const { formName, processChildId, previousProcessChildId } =
            meta?.continueAction?.payload;

          dispatch({
            type: 'formSchemas/multiForm/nextStep',
            payload: {
              formName,
              processChildId,
              data: {
                formSchema: { ...data, formName: processChildId },
                previousProcessChildId
              }
            }
          });
        }
      }
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* openNextForm(
  action: ItemLocalAction<{
    formSchema: Record<string, any>;
    previousProcessChildId: string;
    formName: string;
  }>
) {
  const { formSchema, previousProcessChildId, formName } = action.payload;

  // get data processChildId chain
  const formValues = yield* getItem(`formValues.${formName}`);
  const formSchemas = yield* getItem(`formSchemas.${formName}`);

  let currentProcessChildId = previousProcessChildId;

  let initialValues = {};

  while (currentProcessChildId) {
    const value = formValues[currentProcessChildId];
    const form = formSchemas[currentProcessChildId];

    initialValues = { ...initialValues, ...value };

    currentProcessChildId = form?.previousProcessChildId;
  }

  // call next form
  const { dialogBackend } = yield* getGlobalContext();

  yield dialogBackend.present({
    component: 'core.dialog.RemoteForm',
    props: {
      formSchema,
      initialValues
    }
  });
}

function* nextMultiStepForm(
  action: ItemLocalAction<{
    values: Record<string, any>;
    processChildId: string;
    previousProcessChildId: string;
    formName: string;
    responseData: Record<string, any>;
  }>
) {
  try {
    const {
      values,
      processChildId,
      previousProcessChildId,
      formName,
      responseData
    } = action.payload;

    // save pre form data to redux.
    yield put({
      type: 'formValues/multiForm/nextStep',
      payload: {
        formName,
        processChildId: previousProcessChildId,
        data: values
      }
    });

    // save new form schema to redux.
    yield put({
      type: 'formSchemas/multiForm/nextStep',
      payload: {
        formName,
        processChildId,
        data: {
          formSchema: { ...responseData, formName: processChildId },
          previousProcessChildId
        }
      }
    });

    // call next form
    yield put({
      type: 'nextMultiStepForm/continue',
      payload: {
        formSchema: { ...responseData },
        previousProcessChildId,
        formName
      }
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* previousStepForm(
  action: ItemLocalAction<{ formName: string; previousProcessChildId: string }>
) {
  const { formName, previousProcessChildId } = action.payload;

  try {
    const formValues = yield* getItem(
      `formValues.${formName}.${previousProcessChildId}`
    );

    const formSchema = yield* getItem(
      `formSchemas.${formName}.${previousProcessChildId}`
    );

    const { dialogBackend } = yield* getGlobalContext();

    yield dialogBackend.present({
      component: 'core.dialog.RemoteForm',
      props: {
        formSchema: formSchema.formSchema,
        initialValues: formValues
      }
    });
  } catch (err) {
    yield* handleActionError(err);
  }
}

function* submitMultiForm(action: ItemLocalAction<{ payment_url: string }>) {
  yield;
}

const sagas = [
  takeEvery('multiStepForm/init', openMultiStepForm),
  takeEvery('nextMultiStepForm/continue', openNextForm),
  takeEvery('multiStepForm/previous', previousStepForm),
  takeEvery('multiStepForm/next', nextMultiStepForm),
  takeEvery('multiStepForm/done', submitMultiForm)
];

export default sagas;
