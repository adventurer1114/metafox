/**
 * @type: itemView
 * name: friend.itemView.mainCard
 */
import {
  actionCreators,
  connectItemView
} from '../../../hocs/connectFriendItemView';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
