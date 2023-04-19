/**
 * @type: saga
 * name: photo.saga.photoTags
 */

import {
  fulfillEntity,
  getGlobalContext,
  getItem,
  LocalAction,
  patchEntity
} from '@metafox/framework';
import { concat, without } from 'lodash';
import { takeEvery } from 'redux-saga/effects';

export function* onAddPhotoTag(
  action: LocalAction<{ identity: string; data: Record<string, any> }>
) {
  const {
    payload: { identity, data }
  } = action;
  const item = yield* getItem(identity);
  const { apiClient, normalization } = yield* getGlobalContext();

  if (!item) return;

  const sendData = {
    item_id: item.id,
    tag_user_id: data.content?.id,
    px: data.px,
    py: data.py
  };

  const response = yield apiClient.request({
    url: '/photo-tag',
    method: 'post',
    data: sendData
  });

  const result = yield normalization.normalize(response.data.data);
  const refresh = yield* getItem(identity);

  yield* fulfillEntity(result.data);

  yield* patchEntity(identity, {
    tagged_friends: concat(refresh.tagged_friends ?? [], result.ids)
  });
}

export function* onRemovePhotoTag(
  action: LocalAction<{ identity: string; id: string }>
) {
  const {
    payload: { identity, id }
  } = action;
  const item = yield* getItem(identity);
  const { apiClient } = yield* getGlobalContext();

  if (!item?.tagged_friends) return;

  const _identity = `photo.entities.photo_tag.${id}`;

  yield* patchEntity(identity, {
    tagged_friends: without(item.tagged_friends, _identity)
  });

  yield apiClient.request({
    url: `/photo-tag/${id}`,
    method: 'delete'
  });
}

const sagas = [
  takeEvery('photo/onRemovePhotoTag', onRemovePhotoTag),
  takeEvery('photo/onAddPhotoTag', onAddPhotoTag)
];

export default sagas;
