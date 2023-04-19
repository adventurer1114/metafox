/**
 * @type: route
 * name: page.pending
 * path: pages/pending, page/pending
 * chunkName: pages.page
 * bundle: web
 */
import { createBrowseItemPage } from '@metafox/framework';

export default createBrowseItemPage({
  appName: 'page',
  resourceName: 'page',
  pageName: 'page.pending',
  categoryName: 'page-category',
  defaultTab: 'pending',
  loginRequired: true
});
