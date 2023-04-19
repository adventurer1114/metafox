/**
 * @type: itemView
 * name: group_announcement.itemView.mainCard
 */

import { connectItemView } from '../../../hocs/connectGroupItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, () => {});
