/**
 * @type: saga
 * name: shareOnPageProfile
 */

import {
  getGlobalContext,
  getItem,
  getResourceAction,
  LocalAction
} from '@metafox/framework';
import { isString } from 'lodash';
import { takeLatest } from 'redux-saga/effects';
import { APP_SAVED, DEFAULT_PRIVACY } from '../constants';

const APP_PAGE = 'page';

export function* shareOnPageProfile(action: LocalAction<{ identity: string }>) {
  const {
    payload: { identity }
  } = action;
  const { getPageParams } = yield* getGlobalContext();
  let item = yield* getItem(identity);
  const pageParams: any = getPageParams();

  if (!item) return;

  if (
    item.embed_object &&
    isString(item.embed_object) &&
    (item.embed_object.startsWith('feed') || item.resource_name === APP_SAVED)
  ) {
    item = yield* getItem(item.embed_object);
  }

  const { jsxBackend, dialogBackend } = yield* getGlobalContext();
  const resourceName = item.resource_name;
  const embedView = `${resourceName}.embedItem.insideFeedItem`;

  if (!jsxBackend.get(embedView)) return;

  const remoteDataSource = yield* getResourceAction(
    APP_PAGE,
    APP_PAGE,
    'viewAll'
  );

  const selectedItem = yield dialogBackend.present({
    component: 'pages.dialog.PagesPicker',
    props: {
      remoteDataSource
    }
  });

  if (!selectedItem) return;

  const parentUser = {
    item_type: selectedItem.resource_name,
    item_id: selectedItem.id,
    name: selectedItem.title
  };

  yield dialogBackend.present({
    component: 'feed.status.statusComposerDialog',
    props: {
      pageParams,
      data: {
        attachmentType: 'share',
        privacy: DEFAULT_PRIVACY,
        parentUser,
        attachments: {
          shareItem: {
            as: 'StatusComposerAttatchedShareItem',
            type: APP_PAGE,
            value: {
              embedView,
              identity: item._identity
            },
            pages: [selectedItem.id]
          }
        }
      },
      title: 'share_page',
      hidePrivacy: true
    }
  });
}

const sagas = [takeLatest('shareOnPageProfile', shareOnPageProfile)];

export default sagas;
