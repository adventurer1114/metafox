/**
 * @type: itemView
 * name: notification.itemView.mainCard
 */

import {
  actionCreators,
  connectItemView
} from '@metafox/notification/hocs/connectNotificationItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
