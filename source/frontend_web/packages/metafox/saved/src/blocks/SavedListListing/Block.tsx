/**
 * @type: block
 * name: saved.block.savedListListingBlock
 * title: Saved Lists
 * keywords: saved
 * description: Display saved collections.
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

export default createBlock<ListViewBlockProps>({
  name: 'SavedListListingBlock',
  extendBlock: 'core.block.listview',
  overrides: {
    contentType: 'saved_list',
    dataSource: { apiUrl: '/saveditems-collection' }
  },
  defaults: {
    title: 'My Collections',
    blockProps: {
      variant: 'contained',
      contentStyle: {
        pt: 2,
        pb: 2,
        pl: 2,
        pr: 2,
        bgColor: 'paper',
        borderRadius: 'base'
      }
    },
    itemView: 'saved_collection_list.itemView.mainCard',
    gridContainerProps: { spacing: 2 },
    gridItemProps: { xs: 12, sm: 12, md: 12, lg: 12, xl: 12 }
  }
});
