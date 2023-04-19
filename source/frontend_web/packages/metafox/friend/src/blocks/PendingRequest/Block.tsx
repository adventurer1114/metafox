/**
 * @type: block
 * name: friend.block.pendingRequest
 * title: Pending Friend Requests
 * keywords: friend
 * description: Display pending friend requests.
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const PendingFriendRequestListingBlock = createBlock<ListViewBlockProps>({
  name: 'PendingFriendRequestListingBlock',
  extendBlock: 'core.block.listview',
  defaults: {
    title: 'Pending request',
    itemView: 'friend_request.itemView.mainCard',
    emptyPage: 'core.block.no_content'
  }
});

export default PendingFriendRequestListingBlock;
