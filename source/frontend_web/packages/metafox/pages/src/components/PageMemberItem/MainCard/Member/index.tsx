/**
 * @type: itemView
 * name: page_member.itemView.mainCard
 */

import { connectItemView } from '@metafox/framework';
import actionCreators from '../../../../actions/pagesItemActions';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
