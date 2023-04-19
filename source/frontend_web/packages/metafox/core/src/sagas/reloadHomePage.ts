/**
 * @type: saga
 * name: core.reloadHomePage
 */

import { LocalAction, PAGINATION_REFRESH } from '@metafox/framework';
import { put, takeEvery } from 'redux-saga/effects';

function* reloadHomePage({ payload }: LocalAction<string>) {
  // accept reload home page only
  if (payload !== '/') return;

  if (window?.scrollTo) {
    window.scrollTo({ left: 0, top: 0, behavior: 'smooth' });
  }

  // reload feed
  yield put({
    type: PAGINATION_REFRESH,
    payload: {
      apiUrl: '/feed',
      apiParams: {
        view: 'latest'
      },
      pagingId: '/feed'
    }
  });
}

const sagas = [takeEvery('@app/reloadPage', reloadHomePage)];

export default sagas;
