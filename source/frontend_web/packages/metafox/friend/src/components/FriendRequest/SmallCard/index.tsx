/**
 * @type: itemView
 * name: friend_request.itemView.smallCard
 */
import {
  actionCreators,
  connectItemView
} from '../../../hocs/connectFriendRequestItemView';
import FriendRequest from './ItemView';

export default connectItemView(FriendRequest, actionCreators);
