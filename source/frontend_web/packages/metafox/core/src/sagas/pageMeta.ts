/**
 * @type: saga
 * name: core.pageMeta
 */
import {
  getGlobalContext,
  getPageMetaDataSelector,
  LOAD_PAGE_META,
  LocalAction,
  MFOX_BUILD_TYPE
} from '@metafox/framework';
import { put, select, takeLatest } from 'redux-saga/effects';

export function* loadPageMeta({
  payload: { pathname }
}: LocalAction<{ pathname: string }>) {
  try {
    const { apiClient } = yield* getGlobalContext();

    if (!pathname) return;

    if (MFOX_BUILD_TYPE === 'installation') {
      return;
    }

    let data = yield select(getPageMetaDataSelector, pathname);

    if (data) return;

    // get current url?

    data = yield apiClient
      .post('seo/meta', { url: pathname.replace(/^\/|\/$/g, '') })
      .then(x => x.data?.data);

    yield put({ type: 'pageMeta/put', payload: { id: pathname, data } });
  } catch (err) {
    //
  }
}

const sagas = [takeLatest(LOAD_PAGE_META, loadPageMeta)];

export default sagas;
