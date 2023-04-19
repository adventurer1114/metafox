/**
 * @type: block
 * name: feed.block.pageProfileFeeds
 * title: Page's Feed
 * keywords: feed
 * description:
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const ProfileFeedListingBlock = createBlock<ListViewBlockProps>({
  name: 'PageProfileFeedListingBlock',
  extendBlock: 'core.block.listview',
  overrides: {
    gridItemProps: { xs: 12, sm: 12, md: 12, lg: 12, xl: 12 },
    showWhen: ['falsy', 'profile.is_pending']
  },
  defaults: {
    title: 'Activities',
    blockProps: { variant: 'plained', noHeader: true },
    itemView: 'feed.itemView.mainCard',
    itemProps: {
      showActionMenu: true,
      menuName: 'itemActionMenuForProfile'
    },
    gridContainerProps: { spacing: 2 },
    canLoadMore: true,
    canLoadSmooth: true,
    emptyPage: 'core.block.no_content_with_description',
    dataSource: {
      apiUrl: '/feed',
      apiParams: 'user_id=:id'
    }
  }
});

export default ProfileFeedListingBlock;
