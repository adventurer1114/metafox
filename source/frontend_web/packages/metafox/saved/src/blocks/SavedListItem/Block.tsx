/**
 * @type: block
 * name: saved.block.SavedListItem
 * title: Saved List Items
 * keywords: saved list
 * description: Display saved list items.
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';
import Base from './Base';

export default createBlock<ListViewBlockProps>({
  name: 'SavedListItem',
  extendBlock: Base,
  overrides: {
    contentType: 'saved'
  },
  defaults: {
    title: 'saved_items'
  }
});
