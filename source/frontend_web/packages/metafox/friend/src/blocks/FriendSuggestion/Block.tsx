/**
 * @type: block
 * name: friend.block.suggestions
 * title: Friend Suggestions
 * keywords: friend
 * description:
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const FriendListingBlock = createBlock<ListViewBlockProps>({
  name: 'FriendSuggestions',
  extendBlock: 'core.block.listview',
  overrides: {
    contentType: 'friend',
    dataSource: { apiUrl: '/friend', apiParams: 'view=suggestion&limit=4' }
  },
  defaults: {
    title: 'Friend Suggestions',
    gridContainerProps: {
      spacing: 2
    },
    blockProps: {
      variant: 'plained',
      noHeader: false
    },
    gridItemProps: { xs: 12, md: 12 },
    itemView: 'friend_suggestion.itemView.smallCard'
  }
});

export default FriendListingBlock;
