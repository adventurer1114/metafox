/**
 * @type: route
 * name: forum.view
 * path: /forum/:id(\d+)/:slug?
 * chunkName: pages.forum
 * bundle: web
 */

import { createLandingPage } from '@metafox/framework';

export default createLandingPage({
  appName: 'forum',
  pageName: 'forum.view',
  resourceName: 'forum',
  defaultTab: 'forumDetail'
});
