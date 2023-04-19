/**
 * @type: block
 * name: core.block.AdminStoreApps
 * title: MetaFox Store
 * bundle: admincp
 * experiment: true
 */

import { createBlock, ListViewBlockProps } from '@metafox/framework';

export default createBlock<ListViewBlockProps>({
  extendBlock: 'core.block.listview',
  overrides: {
    contentType: 'app_store_product',
    itemProps: { showActionMenu: false },
    canLoadMore: true
  },
  defaults: {
    title: 'All Apps',
    itemView: 'app_store_product.itemView.mainCard',
    blockLayout: 'Admin Form',
    gridLayout: 'App Store Product - Main Cards'
  }
});
