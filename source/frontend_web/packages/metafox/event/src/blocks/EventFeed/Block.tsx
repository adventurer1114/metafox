/**
 * @type: block
 * name: feed.block.eventProfileFeeds
 * title: Event's Feed
 * keywords: feed, profile, event
 * description: Display feed in event profile
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const ProfileEventFeedListingBlock = createBlock<ListViewBlockProps>({
  name: 'EventProfileFeedListingBlock',
  extendBlock: 'core.block.listview',
  defaults: {
    itemView: 'feed.itemView.mainCard',
    canLoadMore: true,
    canLoadSmooth: true,
    emptyPage: 'core.block.no_content_with_description',
    blockLayout: 'Main Listings',
    gridLayout: 'Feed - Main Card',
    itemLayout: 'Feed - Main Card',
    dataSource: {
      apiUrl: '/feed',
      apiParams: 'user_id=:id'
    }
  }
});

export default ProfileEventFeedListingBlock;
