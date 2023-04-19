/**
 * @type: block
 * name: feed.block.eventProfileFeed
 * title: Event's Feed
 * keywords: feed, profile, event
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const ProfileFeedItemBlock = createBlock<ListViewBlockProps>({
  name: 'EventFeedItemBlock',
  extendBlock: 'core.block.listview',
  defaults: {
    canLoadMore: false,
    moduleName: 'feed',
    resourceName: 'feed',
    actionName: 'viewOwnerItem',
    itemView: 'feed.itemView.mainCard'
  }
});

export default ProfileFeedItemBlock;
