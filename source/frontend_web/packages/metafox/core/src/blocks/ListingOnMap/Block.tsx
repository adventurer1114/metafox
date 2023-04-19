/**
 * @type: block
 * name: core.block.listingMap
 * title: Main map
 * keywords: map listing
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';
import Base from './Base';

export default createBlock<ListViewBlockProps>({
  extendBlock: Base
});
