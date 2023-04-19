/**
 * @type: block
 * name: notification.block.notificationListingBlock
 * title: Notifications
 * keywords: notification
 * description: Display notification items of current logged users.
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const NotificationListingBlock = createBlock<ListViewBlockProps>({
  name: 'NotificationListingBlock',
  extendBlock: 'core.block.listview',
  defaults: {
    title: 'Notifications',
    itemView: 'notification.itemView.mainCard',
    contentType: 'notification',
    dataSource: { apiUrl: '/notification' }
  }
});

export default NotificationListingBlock;
