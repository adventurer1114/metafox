/**
 * @type: block
 * name: feed.block.profileFeeds
 * title: Profile Feeds
 * keywords: feed, profile
 * description:
 * thumbnail:
 */
import {
  connectItemView,
  connectSubject,
  createBlock,
  ListViewBlockProps
} from '@metafox/framework';
import Base from './Base';

const Enhance = connectSubject(connectItemView(Base, () => {}));

const ProfileFeedListingBlock = createBlock<ListViewBlockProps>({
  name: 'ProfileFeedListingBlock',
  extendBlock: Enhance,
  overrides: {
    dataSource: {
      apiUrl: '/feed',
      apiParams: 'user_id=:id',
      pagingId: '/feed?user_id=:id'
    },
    gridVariant: 'listView',
    emptyPage: 'core.block.no_content_with_description'
  },
  defaults: {
    title: 'Activities',
    itemView: 'feed.itemView.mainCard',
    canLoadMore: true,
    canLoadSmooth: true
  }
});

export default ProfileFeedListingBlock;
