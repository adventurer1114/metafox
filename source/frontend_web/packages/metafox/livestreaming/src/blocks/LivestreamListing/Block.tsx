/**
 * @type: block
 * name: livestreaming.block.listingBlock
 * title: Livestream
 * keywords: livestream
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const LivestreamListingBlock = createBlock<ListViewBlockProps>({
  name: 'LivestreamListingBlock',
  extendBlock: 'core.block.listview',
  defaults: {
    title: 'live_videos',
    itemView: 'live_video.itemView.mainCard',
    gridContainerProps: { spacing: 2 },
    gridItemProps: { xs: 12, sm: 12, md: 6, lg: 6, xl: 6 },
    emptyPage: 'core.block.no_content'
  }
});

export default LivestreamListingBlock;
