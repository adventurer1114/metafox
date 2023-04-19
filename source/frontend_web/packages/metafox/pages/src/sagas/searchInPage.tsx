/**
 * @type: saga
 * name: saga.page.searchInPage
 */

import { getGlobalContext, getItem, ItemLocalAction } from '@metafox/framework';
import { takeEvery } from 'redux-saga/effects';

export function* searchInPage(action: ItemLocalAction) {
  const {
    payload: { identity }
  } = action;
  const item = yield* getItem(identity);

  const { dialogBackend, i18n } = yield* getGlobalContext();

  dialogBackend.present({
    component: 'core.dialog.searchInModule',
    props: {
      title: i18n.formatMessage({ id: 'search_this_page' }),
      placeholder: i18n.formatMessage({
        id: 'search_posts_photos_videos_and_more'
      }),
      item
    }
  });
}

const sagas = [takeEvery('page/search', searchInPage)];

export default sagas;
