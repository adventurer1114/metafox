/**
 * @type: block
 * name: feed.block.groupProfileFeeds
 * title: Group's Feed
 * keywords: feed, profile, group
 * description: Display feed in group profile
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const ProfileFeedListingBlock = createBlock<ListViewBlockProps>({
  name: 'GroupProfileFeedListingBlock',
  extendBlock: 'core.block.listview',
  defaults: { canLoadMore: false },
  overrides: {
    showWhen: ['falsy', 'profile.is_pending']
  }
});

export default ProfileFeedListingBlock;
