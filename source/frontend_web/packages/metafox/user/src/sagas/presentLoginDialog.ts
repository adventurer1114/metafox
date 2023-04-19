/**
 * @type: saga
 * name: user.saga.showDialogLogin
 */

import { getGlobalContext, handleActionError } from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

function* showDialogLogin() {
  const { dialogBackend } = yield* getGlobalContext();

  try {
    yield dialogBackend.present({
      component: 'user.dialog.LoginDialog'
    });
  } catch (error) {
    yield* handleActionError(error);
  }
}

const sagas = [takeEvery('user/showDialogLogin', showDialogLogin)];

export default sagas;
