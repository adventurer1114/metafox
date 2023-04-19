/**
 * @type: block
 * name: core.block.searchBox
 * title: Search Box
 * keywords: sidebar
 * description:
 * thumbnail:
 */

import { connectAppUI, createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

const Enhance = connectAppUI(Base);

export default createBlock<Props>({
  extendBlock: Enhance,
  defaults: {
    placeholder: 'Search',
    blockLayout: 'sidebar search box'
  }
});
