/**
 * @type: itemView
 * name: event.itemView.upcomingCard
 */

import {
  actionCreators,
  connectItemView
} from '@metafox/event/hocs/connectEventItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
