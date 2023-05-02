/**
 * @type: itemView
 * name: live_video.embedItem.insideFeedItem
 */

import {
  actionCreators,
  connectItemView
} from '@metafox/livestreaming/hocs/connectLivestreamItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
