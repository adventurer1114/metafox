/**
 * @type: saga
 * name: feed.saga.statusComposer
 */

import { fetchDetailSaga } from '@metafox/core/sagas/fetchDetailSaga';
import { shouldHidePrivacy, shouldShowTypePrivacy } from '@metafox/feed/utils';
import {
  Address,
  BasicFileItem,
  FETCH_DETAIL,
  getGlobalContext,
  getItem,
  getSession,
  handleActionError,
  handleActionFeedback,
  LinkShape,
  LocalAction,
  makeDirtyPaging,
  // makeDirtyPaging,
  PAGINATION_PUSH_INDEX,
  StatusComposerState
} from '@metafox/framework';
import { DEFAULT_PRIVACY } from '@metafox/share/constants';
import { getSharingItemPrivacy } from '@metafox/user/sagas';
import { isVideoType } from '@metafox/utils';
import { get, isArray, isEmpty, isMatch, isNil, pick, unset } from 'lodash';
import { all, call, put, takeLatest } from 'redux-saga/effects';
import { getTotalPins } from './getTotalPins';

const compareObjWithKeys = (a, b, keys) => isMatch(a, pick(b, keys));

type SubmitAction = LocalAction<
  {
    composerState: StatusComposerState;
    initValue: StatusComposerState;
    text: string;
    isEdit: boolean;
    id: string;
    parentIdentity: string;
    parentUser: Record<string, any>;
  },
  {
    onSuccess: (data?: any) => void;
    onFailure: (err?: any) => void;
  }
>;

export type StatusComposerPostConfig = {
  action: string;
  data: {
    item_type?: string;
    item_id?: string;
    user_status: string;
    post_content?: string;
    post_type: string;
    friends?: Array<number>;
    location?: Address;
    tagged_friends?: number[];
    privacy: number;
    photo_files?: Record<string, any>[];
    video_file?: Record<any, string>;
    video_url?: string;
    video_title?: string;
    video_description?: string;
    video_files?: Record<'new', number[]>;
    status_background_id?: number;
    photo_description?: string;
    parent_item_id?: string;
    post_as_parent?: boolean;
    parent_user_id?: string;
    parent_item_type?: string;
    link_image?: string;
    link_description?: string;
    link_title?: string;
    link_url?: string;
    poll_question?: string;
    poll_answers?: Record<string, any>[];
    poll_caption?: string;
    poll_close_time?: string;
    poll_attachments?: Record<string, any>[];
  };
};

export function* onPressStatus(props) {
  const { dialogBackend } = yield* getGlobalContext();

  const privacy = yield* getSharingItemPrivacy('feed');
  const session = yield* getSession();

  const { user } = session || {};
  const { parentIdentity, parentType } = props.payload;
  const parentItem = yield* getItem(parentIdentity);
  const { privacy_detail } = parentItem || {};

  const hidePrivacy = shouldHidePrivacy(parentIdentity, parentType, user);
  const viewTypePrivacy = shouldShowTypePrivacy(parentIdentity, parentType);

  dialogBackend.present({
    component: 'feed.status.statusComposerDialog',
    props: {
      ...props.payload,
      title: 'create_post',
      data: {
        ...props.payload.data,
        privacy: privacy?.value || DEFAULT_PRIVACY,
        privacy_detail
      },
      hidePrivacy,
      viewTypePrivacy
    },
    dialogId: 'statusComposerDialog'
  });
}

function uploadFile(
  apiClient: any,
  fileItem: BasicFileItem
): { error?: string; uid: string; id: number } {
  const formData = new FormData();
  let params: Record<string, any> = {};

  formData.append('file', fileItem.file);
  params = {
    name: 'file',
    type: fileItem.fileItemType,
    item_type: fileItem.fileItemType,
    file_type: fileItem.fileItemType,
    file_name: fileItem.file_name,
    file_size: fileItem.file_size
  };

  // attached to file
  Object.keys(params).forEach(name => {
    formData.append(name, params[name]);
  });

  return apiClient
    .request({
      url: fileItem.upload_url,
      method: 'post',
      data: formData
    })
    .then(response => get(response, 'data.data') || get(response, 'data'))
    .catch(error => ({
      error:
        get(error, 'response.data.errors.file.0') ||
        get(error, 'response.data.error')
    }));
}

export function uploadSingleFile(
  apiClient: any,
  item: BasicFileItem,
  params: Record<string, any>,
  url: string = '/file'
) {
  const formData = new FormData();
  formData.append(params.name, item.file);

  Object.keys(params).forEach(name => {
    formData.append(name, params[name]);
  });

  const type = isVideoType(item.file.type) ? 'video' : 'photo';

  if (type === 'photo' && item.base64) {
    formData.append('base64', item.base64);
  }

  formData.append('type', type);
  formData.append('item_type', type);
  formData.append('file_type', type);

  return apiClient
    .request({
      url,
      method: 'post',
      data: formData
    })
    .then(response => {
      const file = get(response, 'data.data');

      return {
        ...file,
        text: item?.text ?? '',
        tagged_friends: item?.tagged_friends ?? []
      };
    });
}

export function* uploadFiles(
  apiClient: any,
  items: BasicFileItem[],
  params: Record<string, any>,
  url?: string
) {
  try {
    return yield all(
      items.map(item => uploadSingleFile(apiClient, item, params, url))
    );
  } catch (err) {
    yield* handleActionError(err);
  }
}
export function* uploadAttachmentFiles(apiClient: any, items: BasicFileItem[]) {
  try {
    return yield all(items.map(item => uploadFile(apiClient, item)));
  } catch (err) {
    yield* handleActionError(err);
  }
}

export function* getLink(action: SubmitAction) {
  const {
    payload,
    meta: { onSuccess, onFailure }
  } = action;
  const { apiClient } = yield* getGlobalContext();

  try {
    const response = yield apiClient.request({
      method: 'post',
      url: '/link/fetch',
      data: { link: payload }
    });

    if (!response.status) return;

    const { data } = response.data;

    // if (image) {
    //   try {
    //     yield apiClient.request({
    //       method: 'get',
    //       url: image
    //     });
    //   } catch (err) {
    //     console.log(err);
    //     data.image = null;
    //   }
    // }

    typeof onSuccess === 'function' && onSuccess(data);
  } catch (error) {
    typeof onFailure === 'function' && onFailure(payload);
  }
}

export function* onSubmit(action: SubmitAction) {
  const {
    payload: {
      composerState,
      text,
      isEdit,
      id,
      parentIdentity,
      parentUser: parentUserProp,
      initValue
    },
    meta: { onSuccess, onFailure }
  } = action;

  const { apiClient, toastBackend } = yield* getGlobalContext();
  const { user } = yield* getSession();
  const parentUser =
    (yield* getItem<{ resource_name: string; id: string }>(parentIdentity)) ||
    parentUserProp;

  const friends = get(composerState, 'tags.friends.value');
  const place: Address = get(composerState, 'tags.place.value');
  const tagged_friends = isArray(friends) ? friends.map(x => x.id) : undefined;
  const privacy = get(composerState, 'privacy');
  const postType = get(composerState, 'post_type');
  const postAsParent = get(composerState, 'post_as_parent');
  const location = place
    ? {
        address: place.name,
        lat: place.lat,
        lng: place.lng
      }
    : undefined;

  let photos = get(composerState, 'attachments.photo.value');
  const link: LinkShape = get(composerState, 'attachments.link.value');
  const videos = get(composerState, 'attachments.video.value');
  const poll = get(composerState, 'attachments.poll.value');
  const pollAttachments = get(
    composerState,
    'attachments.poll.value.poll_attachments',
    []
  );
  const statusBackground = get(
    composerState,
    'attachments.statusBackground.value'
  );
  const shareValue = get(composerState, 'attachments.shareItem.value');

  const advanceConfig = String(user.id) !== String(parentUser?.id) && {
    parent_item_id: parentUser?.id
  };
  const postConfig: StatusComposerPostConfig = {
    action: isEdit ? `/feed/${id}` : '/feed',
    data: {
      user_status: text,
      post_type: 'activity_post',
      location,
      tagged_friends,
      privacy,
      ...advanceConfig
    }
  };

  if (photos?.length) {
    photos = photos.map(photo => ({
      ...photo,
      tagged_friends: photo.tagged_friends
        ? photo.tagged_friends.map(x => pick(x, ['px', 'py', 'user_id']))
        : []
    }));
    const newFiles = photos.filter(photo => !photo.id);
    const payloadNewFiles = yield call(uploadFiles, apiClient, newFiles, {
      name: 'file'
    });

    if (!payloadNewFiles) return onFailure();

    const payloadFiles = [];

    payloadNewFiles.forEach(item =>
      payloadFiles.push({
        id: item.temp_file,
        type: item.item_type,
        status: 'new',
        text: item.text,
        tagged_friends: item.tagged_friends
      })
    );
    const compareKeys = ['text', 'base64', 'tagged_friends'];
    const initPhotos = initValue.attachments?.photo?.value;
    const payloadFilesEdited =
      initPhotos && initPhotos.length
        ? photos.filter(item => {
            const defaultItem = initPhotos.find(
              x => item?.id && x.id === item.id
            );

            if (!defaultItem) return false;

            if (
              defaultItem.tagged_friends &&
              defaultItem.tagged_friends.length
            ) {
              defaultItem.tagged_friends = defaultItem.tagged_friends.map(
                x => ({
                  user_id: x.user?.id,
                  px: x.px,
                  py: x.py
                })
              );
            }

            const isEqual = compareObjWithKeys(defaultItem, item, compareKeys);

            return !isEqual;
          })
        : [];

    payloadFilesEdited.forEach(item => {
      const updateFiels = pick(item, compareKeys);

      return payloadFiles.push({
        id: item.id,
        type: item.resource_name,
        status: 'edit',
        ...updateFiels
      });
    });
    const currentPhotoIds = photos.map(item => item.id);
    const payLoadRemoveFiles = initPhotos
      ? initPhotos.filter(photo => !currentPhotoIds.includes(photo.id))
      : [];

    payLoadRemoveFiles.forEach(item =>
      payloadFiles.push({
        id: item.id,
        type: item.resource_name,
        status: 'remove'
      })
    );

    postConfig.data.photo_files = payloadFiles.length
      ? payloadFiles
      : undefined;
    postConfig.data.post_type = 'photo_set';
    postConfig.data.photo_description = text;
    postConfig.data.user_status = undefined;
  } else if (videos) {
    const file = yield call(
      uploadFiles,
      apiClient,
      videos,
      {
        name: 'file',
        item_type: 'v'
      },
      '/file/upload-video'
    );

    if (!file) return onFailure();

    postConfig.data.post_type = 'video';
    postConfig.data.video_description = text;
    postConfig.data.video_title = text;
    postConfig.data.video_file = {
      status: 'new',
      temp_file: file[0]
    };
  } else if (!isNil(statusBackground)) {
    if (isEmpty(get(composerState, 'editorStyle')))
      postConfig.data.status_background_id = isEdit ? 0 : undefined;
    else postConfig.data.status_background_id = statusBackground.id;
  } else if (poll) {
    unset(poll, 'submit');
    postConfig.data.post_type = 'poll';
    Object.keys(poll).forEach(key => (postConfig.data[key] = poll[key]));

    if (pollAttachments.length) {
      const newFiles = pollAttachments.filter(attachment => !attachment.id);
      const payloadNewFiles = yield call(
        uploadAttachmentFiles,
        apiClient,
        newFiles
      );

      if (!payloadNewFiles) return onFailure();

      const payloadAttachments = [];

      payloadNewFiles.forEach(item =>
        payloadAttachments.push({
          ...item,
          id: item.id,
          status: 'create'
        })
      );

      postConfig.data.poll_attachments = payloadAttachments.length
        ? payloadAttachments
        : undefined;
    }
  } else if (link) {
    postConfig.data.link_image = link.image;
    postConfig.data.link_description = link.description;
    postConfig.data.post_type = 'link';
    postConfig.data.link_title = link.title;
    postConfig.data.link_url = link.link;
  } else if (shareValue) {
    postConfig.action = '/feed/share';

    const shareItem = yield* getItem(shareValue.identity);

    postConfig.data.item_type =
      shareItem.resource_name || shareItem.module_name || shareItem.item_type;
    postConfig.data.item_id = shareItem.id || shareItem.item_id;
    postConfig.data.post_content = text;
    postConfig.data.post_type = get(
      composerState,
      'attachments.shareItem.type'
    );
    const parentIds = `${postConfig.data.post_type}s`;

    postConfig.data[parentIds] = get(
      composerState,
      `attachments.shareItem.${parentIds}`
    );
  }

  if (postType) {
    postConfig.data.post_type = postType;
  }

  if (postAsParent) {
    postConfig.data.post_as_parent = postAsParent;
  }

  try {
    const response = yield apiClient.request({
      method: isEdit ? 'put' : 'post',
      url: postConfig.action,
      data: postConfig.data
    });

    const ok = 'success' === get(response, 'data.status');
    const message = get(response, 'data.message');
    const feedId = get(response, 'data.data.id');
    const feedIds = get(response, 'data.data.ids');
    const nextAction = get(response, 'data.data.nextAction');

    nextAction && (yield put(nextAction));

    yield* handleActionFeedback(response);

    if (ok && feedId === 0) {
      onSuccess();
      toastBackend.success(message);
    }

    if (!ok || (!feedId && !feedIds?.length)) {
      onSuccess();

      return;
    }

    const id = feedId || feedIds[0];

    // support for both v4, v5
    feedId && (yield* updateFeedItem(id, parentUser, !isEdit));
    feedIds &&
      (yield* feedIds.map((id: number) =>
        call(updateFeedItem, id, parentUser, !isEdit)
      ));

    yield* makeDirtyPaging(postConfig.data.post_type, undefined, false);

    onSuccess();

    // scroll to new post
    if (!isEdit) {
      setImmediate(() => {
        const element = document.getElementById(
          `homefeed_feed.entities.feed.${feedId}`
        );

        if (!element) return;

        const yOffset = -60;
        const eleTop = element.getBoundingClientRect().top;
        const heightScreen = window.innerHeight;

        if (eleTop < heightScreen) return;

        const y = eleTop + window.pageYOffset + yOffset;

        window.scrollTo({ top: y, behavior: 'smooth' });
      });
    }
  } catch (err) {
    onFailure();
    yield* handleActionError(err);
  }
}

export function* updateFeedItem(
  id: number,
  item: Record<string, any>,
  newFeed: boolean,
  exceptEntities?: string[]
) {
  const apiUrl = item ? `feed/${id}?user_id=${item?.id}` : `feed/${id}`;

  const pagingId = !item ? '/feed' : [`/feed?user_id=${item?.id}`, '/feed'];

  const data = yield* fetchDetailSaga({
    payload: {
      apiUrl,
      exceptEntities
    },
    type: FETCH_DETAIL
  });

  const totalPinsHome = yield* getTotalPins(null);
  const totalPinsProfile = yield* getTotalPins(parseInt(item?.id));
  const indexId = !item ? totalPinsHome : [totalPinsProfile, totalPinsHome];

  newFeed &&
    (yield put({
      type: PAGINATION_PUSH_INDEX,
      payload: {
        data,
        pagingId,
        indexId
      }
    }));
}

const sagas = [
  takeLatest('statusComposer/onPress/status', onPressStatus),
  takeLatest('statusComposer/getLink', getLink),
  takeLatest('statusComposer/SUBMIT', onSubmit)
];

export default sagas;
