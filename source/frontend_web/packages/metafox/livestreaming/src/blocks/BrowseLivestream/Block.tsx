/**
 * @type: block
 * name: livestream.block.BrowseLivestreams
 * title: Browse Livestreams
 * keywords: livestream
 * description: Display livestreams
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';
import Base from './Base';

export default createBlock<ListViewBlockProps>({
  extendBlock: Base,
  defaults: {
    title: 'Livestreams',
    itemView: 'live_video.itemView.mainCard',
    blockLayout: 'Main Listings',
    gridLayout: 'Live_video - Main Card'
  }
});
