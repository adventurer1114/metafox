/**
 * @type: saga
 * name: shareOnGroupProfile
 */

import {
  getGlobalContext,
  getItem,
  getResourceAction,
  ItemLocalAction,
  fulfillEntity
} from '@metafox/framework';
import { isString } from 'lodash';
import { takeLatest } from 'redux-saga/effects';
import { APP_SAVED } from '../constants';

const APP_GROUP = 'group';

export function* shareOnGroupProfile(action: ItemLocalAction) {
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

  const { jsxBackend, dialogBackend, normalization } =
    yield* getGlobalContext();
  const resourceName = item.resource_name;
  const embedView = `${resourceName}.embedItem.insideFeedItem`;

  if (!jsxBackend.get(embedView)) return;

  const remoteDataSource = yield* getResourceAction(
    APP_GROUP,
    APP_GROUP,
    'viewAll'
  );

  const selectedItem = yield dialogBackend.present({
    component: 'group.dialog.GroupPicker',
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

  const result = normalization.normalize(selectedItem);
  yield* fulfillEntity(result.data);

  yield dialogBackend.present({
    component: 'feed.status.statusComposerDialog',
    props: {
      pageParams,
      parentIdentity: `${APP_GROUP}.entities.${selectedItem.resource_name}.${selectedItem.id}`,
      parentType: selectedItem?.resource_name,
      data: {
        attachmentType: 'share',
        parentUser,
        attachments: {
          shareItem: {
            as: 'StatusComposerAttatchedShareItem',
            type: APP_GROUP,
            value: {
              embedView,
              identity: item._identity
            },
            groups: [selectedItem.id]
          }
        }
      },
      title: 'share_group',
      hidePrivacy: true
    }
  });
}

const sagas = [takeLatest('shareOnGroupProfile', shareOnGroupProfile)];

export default sagas;
