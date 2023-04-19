/**
 * @type: itemView
 * name: page.itemView.mainCard
 */

import { actionCreators, connectItemView } from '../../../hocs/connectPageItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
