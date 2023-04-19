/**
 * @type: route
 * name: pages.profile
 * path: /pages/:id(\d+)/:tab?, /page/:id(\d+)/:tab?
 * chunkName: pages.page
 * bundle: web
 */

import { createProfilePage } from '@metafox/framework';

export default createProfilePage({
  appName: 'page',
  resourceName: 'page',
  pageName: 'page.profile'
});
