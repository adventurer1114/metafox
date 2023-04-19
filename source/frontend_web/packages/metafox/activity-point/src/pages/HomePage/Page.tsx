/**
 * @type: route
 * name: activity_point.home
 * path: /activitypoint, /activitypoint/transactions
 * chunkName: pages.activitypoint
 * bundle: web
 */

import { createLandingPage } from '@metafox/framework';

export default createLandingPage({
  appName: 'activitypoint',
  pageName: 'activity_point.home',
  resourceName: 'activitypoint',
  defaultTab: 'transactions',
  loginRequired: true
});
