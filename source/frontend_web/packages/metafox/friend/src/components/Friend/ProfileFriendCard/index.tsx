/**
 * @type: itemView
 * name: friend.itemView.profileFriendCard
 */
import {
  actionCreators,
  connectItemView
} from '../../../hocs/connectFriendItemView';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
