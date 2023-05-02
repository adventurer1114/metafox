/**
 * @type: block
 * name: livestream.block.livestreamListingFeaturedBlock
 * title: Livestreams
 * keywords: livestream
 * description: Display featured livestream items.
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const LivestreamListingFeaturedBlock = createBlock<ListViewBlockProps>({
  name: 'LivestreamListingBlock',
  extendBlock: 'core.block.listview',
  overrides: {
    contentType: 'livestream',
    dataSource: { apiUrl: '/livestream', apiParams: 'view=friend&limit=4' }
  },
  defaults: {
    title: 'Livestreams Featured',
    blockProps: { variant: 'contained' },
    itemView: 'livestream.itemView.smallCard',
    gridContainerProps: { spacing: 2 },
    gridItemProps: { xs: 12, sm: 12, md: 12, lg: 12, xl: 12 }
  }
});

export default LivestreamListingFeaturedBlock;
