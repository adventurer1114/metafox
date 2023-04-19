import { FormikHelpers } from 'formik';
import { get, isArray, isObject, set } from 'lodash';
import getGlobalContext from './getGlobalContext';

const setObject = (errors: Record<string, string>) => {
  const result = {};

  if (!isObject(errors)) return errors;

  Object.keys(errors).forEach(name => {
    set(result, name, errors[name]);
  });

  return result;
};

export default function* handleActionError(
  error: any,
  form?: FormikHelpers<any>
) {
  if (!error) return;

  const { i18n, dispatch } = yield* getGlobalContext();

  const errors = get(error, 'response.data.errors');

  if (form && errors) {
    form.setErrors(setObject(errors));

    return;
  }

  const msgErrors = isObject(errors)
    ? Object.keys(errors)
        .map(err => {
          return isArray(errors[err]) ? errors[err].join(', ') : undefined;
        })
        .filter(item => item)
    : [errors];

  let message =
    msgErrors.join(' ') ||
    get(error, 'response.data.message') ||
    get(error, 'response.data.error') ||
    error.message;

  const { dialogBackend } = yield* getGlobalContext();

  let title = 'oops';

  try {
    const {
      message: messageServer,
      title: titleServer,
      action
    } = JSON.parse(get(error, 'response.data.error'));

    if (action) {
      dispatch({ type: action });

      return;
    }

    message = messageServer;
    title = titleServer;
  } catch (err) {}

  yield dialogBackend.alert({
    title: i18n.formatMessage({ id: title }),
    message
  });
}
