/**
 * @type: itemView
 * name: search.itemView.mainCard
 */

import {
  actionCreators,
  connectItemView
} from '@metafox/search/hocs/connectSearchItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
