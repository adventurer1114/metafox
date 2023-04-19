/**
 * @type: route
 * name: page.myPending
 * path: pages/my, page/my
 * chunkName: pages.page
 * bundle: web
 */
import { createLandingPage } from '@metafox/framework';

export default createLandingPage({
  appName: 'page',
  resourceName: 'page',
  pageName: 'page.myPending',
  categoryName: 'page-category',
  defaultTab: 'my',
  loginRequired: true
});
