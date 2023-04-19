/**
 * @type: saga
 * name: core.updateFeedItem
 */
import { StatusComposerDialogProps } from '@metafox/feed/dialogs/StatusComposer/Base';
import { shouldHidePrivacy, shouldShowTypePrivacy } from '@metafox/feed/utils';
import {
  deleteEntity,
  getGlobalContext,
  getItem,
  getItemActionConfig,
  getSession,
  ItemLocalAction
} from '@metafox/framework';
import { getImageSrc } from '@metafox/utils';
import { takeEvery } from 'redux-saga/effects';

const embedObjectToAttachment = ({
  embed_object,
  status_background_id
}: Record<string, any>) => {
  const embedState = Object.assign({}, embed_object);

  const { resource_name } = embedState;
  let attachments = {};

  if (status_background_id) {
    attachments = {
      statusBackground: {
        value: {
          id: status_background_id
        }
      }
    };

    return { attachments, attachmentType: 'backgroundStatus' };
  }

  switch (resource_name) {
    case 'link':
      attachments = {
        link: {
          as: 'StatusComposerControlPreviewLink',
          value: embedState
        }
      };
      break;
    case 'photo':
      attachments = {
        photo: {
          as: 'StatusComposerControlAttachedPhotos',
          value: [embedState]
        }
      };
      break;
    case 'photo_set':
      attachments = {
        photo: {
          as: 'StatusComposerControlAttachedPhotos',
          value: embedState.photos
        }
      };
      break;
  }

  return { attachments, attachmentType: resource_name };
};

function* deleteFeed(action: ItemLocalAction & { payload: { id: string } }) {
  const { id } = action.payload;

  yield* deleteEntity(`feed.entities.feed.${id}`);
}

function* updateFeed(action: ItemLocalAction) {
  const { identity } = action.payload;
  const item = yield* getItem(identity);
  const parentUser = yield* getItem(item?.parent_user);
  const { user } = yield* getSession();

  const { dialogBackend, apiClient, compactUrl } = yield* getGlobalContext();

  const viewTypePrivacy = shouldShowTypePrivacy(
    parentUser?._identity,
    parentUser?.module_name
  );

  const hidePrivacy = shouldHidePrivacy(
    parentUser?._identity,
    parentUser?.module_name,
    user
  );
  const disabledPrivacy = !item?.extra?.can_change_privacy_from_feed;

  try {
    const config = yield* getItemActionConfig(item, 'editItem');

    if (!config.apiUrl) return;

    const rs = yield apiClient.request({
      method: config.apiMethod,
      url: compactUrl(config.apiUrl, item)
    });

    const {
      status_background_id,
      privacy,
      tagged_friends,
      location,
      link,
      embed_object,
      privacy_detail
    } = rs.data.data.item;

    const { post_type, extra } = rs.data.data;

    let tags = {};
    const { attachments, attachmentType } = embedObjectToAttachment({
      embed_object,
      status_background_id
    });
    let { status_text } = rs.data.data.item;

    if (tagged_friends?.length) {
      tags = {
        ...tags,
        friends: {
          as: 'StatusComposerControlTaggedFriends',
          priority: 1,
          value: tagged_friends
        }
      };
    }

    if (location) {
      tags = {
        ...tags,
        place: {
          value: { ...location, name: location?.address },
          as: 'StatusComposerControlTaggedPlace',
          priority: 3
        }
      };
    }

    if (link && !status_text) {
      status_text = link;
    }

    const data = status_background_id
      ? {
          className: 'withBackgroundStatus',
          textAlignment: 'center',
          attachmentType: 'backgroundStatus',
          editorStyle: {
            fontSize: '28px',
            color: 'white',
            textAlign: 'center',
            backgroundSize: 'cover',
            backgroundImage: `url("${getImageSrc(
              item.status_background,
              '1024'
            )}")`,
            minHeight: 371,
            marginTop: 16,
            marginBottom: 16
          }
        }
      : {};

    // TODO: need to update data props in future
    yield dialogBackend.present<void, StatusComposerDialogProps>({
      component: 'feed.status.statusComposerDialog',
      props: {
        data: {
          ...data,
          privacy,
          tags,
          attachments,
          attachmentType,
          post_type,
          privacy_detail,
          extra
        },
        editor: {
          status_text,
          status_background_id
        },
        title: 'edit_post',
        isEdit: true,
        id: item.id,
        parentIdentity: item.parent_user,
        parentType: parentUser?.resource_name,
        hidePrivacy,
        disabledPrivacy,
        viewTypePrivacy
      }
    });
  } catch (err) {
    // console.log(err);
  }
}

const sagas = [
  takeEvery('updateFeed', updateFeed),
  takeEvery('feed/delete', deleteFeed)
];

export default sagas;
