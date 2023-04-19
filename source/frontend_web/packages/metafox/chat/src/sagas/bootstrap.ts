/**
 * @type: saga
 * name: chat.saga.bootstrap
 */

import {
  APP_BOOTSTRAP_DONE,
  getGlobalContext,
  getSession,
  IS_ADMINCP
} from '@metafox/framework';
import { takeLatest, put } from 'redux-saga/effects';

function* bootstrap() {
  const { chatBackend, getSetting } = yield* getGlobalContext();
  const { user } = yield* getSession();

  const setting: any = getSetting();

  if (!user?.id) return;

  try {
    if (IS_ADMINCP || setting?.chatplus?.server || !setting?.chat) return;

    yield chatBackend.listenRoomNotify(user.id);

    yield put({ type: 'chat/room/getChatRoomList' });
  } catch (error) {
    // eslint-disable-next-line no-console
    console.log('bootstrapChat error', error);
  }
}

const sagas = [takeLatest(APP_BOOTSTRAP_DONE, bootstrap)];

export default sagas;
