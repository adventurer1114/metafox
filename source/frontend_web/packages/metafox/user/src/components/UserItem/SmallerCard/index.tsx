/**
 * @type: itemView
 * name: user.itemView.smallerCard
 */
import { actionCreators, connectItemView } from '../../../hocs/connectUserItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
