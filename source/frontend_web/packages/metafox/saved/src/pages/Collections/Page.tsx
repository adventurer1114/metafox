/**
 * @type: route
 * name: saved.list
 * path: /saved/list/:collection_id(\d+), /saved/list/:collection_id(\d+)/:slug?
 * chunkName: pages.saved
 * bundle: web
 */

import { createSearchItemPage } from '@metafox/framework';

export default createSearchItemPage({
  appName: 'saved',
  resourceName: 'saved',
  pageName: 'saved.list'
});
