/**
 * @type: itemView
 * name: friend_request.itemView.mainCard
 */
import {
  actionCreators,
  connectItemView
} from '../../../hocs/connectFriendRequestItemView';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
