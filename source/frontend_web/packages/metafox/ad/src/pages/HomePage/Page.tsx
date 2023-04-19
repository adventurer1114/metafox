/**
 * @type: route
 * name:  ad.home
 * path: /ad
 * chunkName: pages.ad
 * bundle: web
 */
import { createLandingPage } from '@metafox/framework';

export default createLandingPage({
  appName: 'ad',
  pageName: 'ad.home',
  resourceName: 'ad'
});
