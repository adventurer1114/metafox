/**
 * @type: itemView
 * name: friend.itemView.smallCard
 */
import {
  actionCreators,
  connectItemView
} from '../../../hocs/connectFriendItemView';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
