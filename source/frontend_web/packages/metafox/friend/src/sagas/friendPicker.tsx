/**
 * @type: saga
 * name: friend.saga.friendPicker
 */
import {
  getGlobalContext,
  ItemLocalAction,
  getItem,
  getResourceAction
} from '@metafox/framework';
import { SimpleUserShape } from '@metafox/ui/types';
import { takeEvery, put, take } from 'redux-saga/effects';
import { APP_FRIEND, RESOURCE_FRIEND } from '@metafox/friend';

export function* friendPicker({
  payload,
  meta
}: ItemLocalAction<
  { users: Array<SimpleUserShape>; parentIdentity: string; tagType: string },
  { onSuccess: (value) => void }
>) {
  const { dialogBackend, compactData } = yield* getGlobalContext();
  const { users, parentIdentity, tagType } = payload;
  const parentUser = yield* getItem(parentIdentity);
  let initialParams = {};
  let dataSource = {};

  if (tagType === 'member') {
    yield put({ type: 'group/saga/pickerMember/get' });

    const result = yield take('group/saga/pickerMember/response');
    dataSource = result?.payload;

    initialParams = compactData(dataSource?.apiParams, {
      owner_id: parentUser?.id
    });
  } else {
    dataSource = yield* getResourceAction(
      APP_FRIEND,
      RESOURCE_FRIEND,
      'getForMentionFriends'
    );
    initialParams = compactData(dataSource?.apiParams, {});
  }

  const { onSuccess } = meta;
  const value = yield dialogBackend.present({
    component: 'friend.dialog.MultipleFriendPicker',
    props: {
      apiUrl: dataSource?.apiUrl,
      value: users,
      initialParams,
      placeholder:
        tagType === 'member' ? 'search_for_members' : 'search_for_friends'
    }
  });

  onSuccess && onSuccess(value);
}

const sagaEffect = [takeEvery('friend/friendPicker', friendPicker)];

export default sagaEffect;
