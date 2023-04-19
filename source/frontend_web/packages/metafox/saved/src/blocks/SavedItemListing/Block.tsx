/**
 * @type: block
 * name: saved.block.SavedItemListingBlock
 * title: Saved Items
 * keywords: saved
 * description: Display saved items.
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

export default createBlock<ListViewBlockProps>({
  name: 'SavedItemListingBlock',
  extendBlock: 'core.block.listview',
  overrides: {
    contentType: 'saved',
    dataSource: { apiUrl: '/saveditems' },
    itemProps: { showActionMenu: true },
    pagingId: 'paging_saved_items'
  },
  defaults: {
    title: 'saved_items',
    itemView: 'saved.itemView.mainCard'
  }
});
