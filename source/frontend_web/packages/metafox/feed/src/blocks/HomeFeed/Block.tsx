/**
 * @type: block
 * name: feed.block.homeFeeds
 * title: Home Feeds
 * keywords: feed
 * description:
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const HomeFeedListingBlock = createBlock<ListViewBlockProps>({
  name: 'HomeFeedListingBlock',
  extendBlock: 'core.block.listview',
  overrides: {
    contentType: 'feed',
    itemView: 'feed.itemView.mainCard',
    gridVariant: 'listView',
    gridItemProps: { xs: 12 },
    itemProps: { showActionMenu: true },
    emptyPage: 'core.block.no_content_with_description'
  },
  defaults: {
    title: 'Recent Activities',
    dataSource: {
      apiUrl: '/feed',
      apiParams: 'view=latest',
      pagingId: '/feed'
    },
    itemProps: { showActionMenu: true },
    gridContainerProps: { spacing: 2 },
    canLoadMore: true,
    canLoadSmooth: true,
    blockLayout: 'Main Listings',
    gridLayout: 'Feed - Main Card',
    itemLayout: 'Feed - Main Card'
  }
});

export default HomeFeedListingBlock;
