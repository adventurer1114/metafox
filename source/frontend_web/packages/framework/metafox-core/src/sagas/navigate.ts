/**
 * @type: saga
 * name: navigate
 */
import { put, takeEvery } from 'redux-saga/effects';
import { getGlobalContext } from '@metafox/framework';

function* navigateSaga(action: {
  type: 'navigate';
  payload: { url: string; replace: boolean; target?: string };
}) {
  const { url, replace } = action.payload;
  const { navigate } = yield* getGlobalContext();

  if (url === 'reload') {
    yield put({ type: 'navigate/reload' });
  } else if (replace) {
    navigate(url, { replace: true });
  } else {
    navigate(url);
  }
}

function* reloadWindow() {
  if (window.location?.reload) {
    // eslint-disable-next-line
    window.location.reload();
  }

  yield;
}

const sagas = [
  takeEvery('navigate', navigateSaga),
  takeEvery('navigate/reload', reloadWindow)
];

export default sagas;
