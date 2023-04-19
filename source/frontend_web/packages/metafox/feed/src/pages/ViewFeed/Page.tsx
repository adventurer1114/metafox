/**
 * @type: route
 * name: feed.view
 * path: /feed/:id, /activity_post/:id
 * chunkName: pages.feed
 * bundle: web
 */
import { createViewItemPage } from '@metafox/framework';

export default createViewItemPage({
  appName: 'feed',
  resourceName: 'feed',
  pageName: 'feed.view'
});
