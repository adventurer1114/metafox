/**
 * @type: block
 * name: blog.block.BrowseBlogs
 * title: Browse Blogs
 * keywords: blog
 * description: Display blogs
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';
import Base from './Base';

export default createBlock<ListViewBlockProps>({
  extendBlock: Base,
  overrides: {
    contentType: 'blog',
    itemProps: { showActionMenu: true }
  },
  defaults: {
    title: 'Blogs',
    itemView: 'blog.itemView.mainCard',
    blockLayout: 'Main Listings',
    gridLayout: 'Blog - Main Card'
  }
});
