/**
 * @type: saga
 * name: shareToNewFeed
 */

import {
  getGlobalContext,
  getItem,
  getSession,
  ItemLocalAction
} from '@metafox/framework';
import { getSharingItemPrivacy } from '@metafox/user/sagas';
import { fetchDataPrivacy } from '@metafox/user/sagas/sharingItemPrivacy';
import { isString } from 'lodash';
import { takeLatest, call } from 'redux-saga/effects';
import { APP_SAVED, DEFAULT_PRIVACY } from '../constants';

export function* shareToNewsFeed(action: ItemLocalAction) {
  const {
    payload: { identity }
  } = action;
  let item = yield* getItem(identity);
  const { user } = yield* getSession();
  const parentIdentity = `user.entities.user.${user.id}`;
  const { getPageParams } = yield* getGlobalContext();
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

  yield call(fetchDataPrivacy, {
    payload: { id: user.id }
  });

  const privacy = yield* getSharingItemPrivacy('share');

  yield dialogBackend.present({
    component: 'feed.status.statusComposerDialog',
    props: {
      data: {
        attachmentType: 'share',
        privacy: privacy ? privacy.value : DEFAULT_PRIVACY,
        attachments: {
          shareItem: {
            as: 'StatusComposerAttatchedShareItem',
            value: {
              embedView,
              identity: item._identity
            },
            type: 'wall'
          }
        }
      },
      title: 'share_newsfeed',
      parentIdentity,
      parentType: user.resource_name,
      pageParams
    }
  });
}

const sagas = [takeLatest('shareToNewsFeed', shareToNewsFeed)];

export default sagas;
