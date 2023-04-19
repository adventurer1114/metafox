/**
 * @type: saga
 * name: shareOnFriendProfile
 */

import { APP_FEED } from '@metafox/feed';
import {
  getGlobalContext,
  getItem,
  getResourceAction,
  ItemLocalAction
} from '@metafox/framework';
import { isString } from 'lodash';
import { takeLatest } from 'redux-saga/effects';
import { APP_SAVED } from '../constants';

export function* shareOnFriendProfile(action: ItemLocalAction) {
  const {
    payload: { identity }
  } = action;
  const { getPageParams } = yield* getGlobalContext();
  const pageParams: any = getPageParams();
  let item = yield* getItem(identity);

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
  const dataSource = yield* getResourceAction(
    APP_FEED,
    APP_FEED,
    'shareOnFriendProfile'
  );

  if (!jsxBackend.get(embedView)) return;

  const selectedItem = yield dialogBackend.present({
    component: 'friend.dialog.FriendPicker',
    props: { ...dataSource, initialParams: dataSource?.apiParams }
  });

  if (!selectedItem) return;

  const parentUser = {
    item_type: selectedItem.resource_name,
    id: selectedItem.id,
    name: selectedItem.full_name,
    resource_name: selectedItem.resource_name
  };

  yield dialogBackend.present({
    component: 'feed.status.statusComposerDialog',
    props: {
      pageParams,
      data: {
        attachmentType: 'share',
        parentUser,
        attachments: {
          shareItem: {
            as: 'StatusComposerAttatchedShareItem',
            value: {
              embedView,
              identity: item._identity
            },
            type: 'friend',
            friends: [selectedItem.id]
          }
        }
      },
      title: 'share_friend',
      hidePrivacy: true
    }
  });
}

const sagas = [takeLatest('shareOnFriendProfile', shareOnFriendProfile)];

export default sagas;
