/**
 * @type: route
 * name: subscription.my
 * path: /subscription/:tab(my)
 * chunkName: pages.subscription
 * bundle: web
 */
import { createBrowseItemPage } from '@metafox/framework';

export default createBrowseItemPage({
  appName: 'subscription',
  pageName: 'subscription.my',
  resourceName: 'subscription_invoice',
  loginRequired: true
});
