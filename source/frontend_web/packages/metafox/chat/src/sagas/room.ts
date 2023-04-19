/**
 * @type: saga
 * name: chat.saga.room
 */

import {
  fulfillEntity,
  getGlobalContext,
  getItem,
  getResourceAction,
  handleActionConfirm,
  handleActionError,
  ItemLocalAction,
  LocalAction,
  PAGINATION_DELETE,
  PAGINATION_UNSHIFT
} from '@metafox/framework';
import { takeEvery, put } from 'redux-saga/effects';
import { APP_CHAT } from '../constants';
import {
  getRoomItem,
  putRoomMessages,
  removeMessagesRoom,
  removeRoomDock
} from './helpers';

function* handleRoomActive(
  action: ItemLocalAction<
    { rid: string },
    { onSuccess: (value) => void; onFailure?: () => void }
  >
) {
  const rid = action.payload;
  const { onSuccess, onFailure } = action.meta;

  const { apiClient, normalization } = yield* getGlobalContext();

  // todo check latest data.
  try {
    const responseRoom = yield apiClient.request({
      url: `/chat-room/${rid}`
    });

    const dataRoom = responseRoom?.data?.data;

    if (!dataRoom) return;

    const resultRoom = normalization.normalize(dataRoom);
    yield* fulfillEntity(resultRoom);

    const responseMessages = yield apiClient.request({
      url: '/chat',
      params: { room_id: rid, limit: 100 }
    });

    const dataMessages = responseMessages?.data?.data;
    typeof onSuccess === 'function' && onSuccess(dataRoom);

    if (rid) {
      yield put({
        type: 'chat/chatroom/add',
        payload: { rid }
      });
    }

    yield* putRoomMessages(dataMessages);
  } catch (error) {
    typeof onFailure === 'function' && onFailure();
    yield* handleActionError(error);
  }
}

function* handleRoomInactive() {
  yield;
}

function* deleteRoom(action: ItemLocalAction) {
  const { identity: rid } = action.payload;
  const room = yield* getRoomItem(rid);

  if (!room) return;

  const { apiClient, compactUrl } = yield* getGlobalContext();

  const config: any = yield* getResourceAction(APP_CHAT, 'room', 'deleteItem');

  if (!config.apiUrl) return;

  const ok = yield* handleActionConfirm(config);

  if (!ok) return;

  try {
    const result = yield apiClient.request({
      method: config.apiMethod,
      url: compactUrl(config.apiUrl, room)
    });

    if (!result) {
      // handle error
    }

    yield* removeRoomDock(rid);
    yield* removeMessagesRoom(rid);
    yield put({
      type: PAGINATION_DELETE,
      payload: { identity: `chat.entities.room.${rid}` }
    });
  } catch (error) {
    yield* handleActionError(error);
  }
}

export function* updateRoom(
  action: LocalAction<{
    id: string | number;
    total_unseen?: number;
    [key: string]: any;
  }>
) {
  const { normalization, apiClient } = yield* getGlobalContext();
  let room = action.payload;

  const { id } = room;

  if (!id) return;

  const identity = `chat.entities.room.${id}`;

  const item = yield* getItem(identity);

  try {
    if (item) {
      const resultRoom = normalization.normalize(room);

      yield* fulfillEntity(resultRoom.data);
    } else {
      if (!room.module_name || !room.resource_name) {
        const responseRoom = yield apiClient.request({
          url: `/chat-room/${id}`
        });

        room = responseRoom?.data?.data;
      }

      const resultRoom = normalization.normalize(room);

      yield* fulfillEntity(resultRoom.data);
    }

    yield put({
      type: PAGINATION_UNSHIFT,
      payload: {
        data: [identity],
        pagingId: ['pagination.listRooms']
      }
    });
  } catch (error) {
    // yield* handleActionError(error);
  }
}

const sagas = [
  takeEvery('chat/room/active', handleRoomActive),
  takeEvery('chat/room/inactive', handleRoomInactive),
  takeEvery('chat/room/deleteRoom', deleteRoom),
  takeEvery('chat/updateRoom', updateRoom)
];

export default sagas;
