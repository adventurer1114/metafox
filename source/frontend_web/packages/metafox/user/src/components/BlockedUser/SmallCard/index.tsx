/**
 * @type: itemView
 * name: blocked_user.itemView.smallCard
 */
import {
  actionCreators,
  connectItemView
} from '../../../hocs/connectBlockedUser';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
