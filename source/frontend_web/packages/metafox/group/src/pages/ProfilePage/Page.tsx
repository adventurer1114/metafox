/**
 * @type: route
 * name: group.profile
 * path: /group/:id(\d+)/:tab?
 * chunkName: pages.group
 * bundle: web
 */

import { createProfilePage } from '@metafox/framework';

export default createProfilePage({
  appName: 'group',
  resourceName: 'group',
  pageName: 'group.profile'
});
