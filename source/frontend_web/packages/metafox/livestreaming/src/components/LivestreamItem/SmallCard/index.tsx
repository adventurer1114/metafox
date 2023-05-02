/**
 * @type: itemView
 * name: livestream.itemView.smallCard
 */

import {
  actionCreators,
  connectItemView
} from '@metafox/livestreaming/hocs/connectLivestreamItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
