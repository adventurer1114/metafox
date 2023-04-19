/**
 * @type: itemView
 * name: pages.itemView.previewCard
 */

import { actionCreators, connectItemView } from '../../../hocs/connectPageItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
