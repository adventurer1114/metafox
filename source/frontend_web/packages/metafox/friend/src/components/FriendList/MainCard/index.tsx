/**
 * @type: itemView
 * name: friend_list.itemView.mainCard
 */
import {
  actionCreators,
  connectItemView
} from '../../../hocs/connectFriendListItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
