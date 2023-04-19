/**
 * @type: itemView
 * name: group_member.itemView.invitation
 */
import {
  actionCreators,
  connectItemView
} from '../../../../hocs/connectGroupMemberItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
