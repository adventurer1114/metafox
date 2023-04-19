/**
 * @type: block
 * name: core.block.sidebarShortcutMenu
 * title: Shortcut Menu
 * keywords: sidebar
 * description:
 * thumbnail:
 */

import { createBlock, ListViewBlockProps } from '@metafox/framework';

export default createBlock<ListViewBlockProps>({
  extendBlock: 'core.block.listview',
  overrides: {
    authRequired: true,
    dataSource: {
      apiUrl: '/user/shortcut',
      apiParams: ''
    },
    pagingId: '/user/shortcut',
    headerActions: [{ as: 'user.ManageShortcutButton' }]
  },
  defaults: {
    gridVariant: 'listView',
    itemView: 'shortcut.itemView.smallCard',
    gridLayout: 'Shortcut - Menu Items',
    blockLayout: 'sidebar shortcut',
    canLoadMore: true,
    canLoadSmooth: true
  }
});
