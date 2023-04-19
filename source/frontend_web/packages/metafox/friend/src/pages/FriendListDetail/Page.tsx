/**
 * @type: route
 * name: friend.listDetail
 * path: /friend/list/:list_id(\d+)/:slug?
 * chunkName: pages.friend
 * bundle: web
 */
import { createSearchItemPage } from '@metafox/framework';

export default createSearchItemPage({
  appName: 'friend',
  resourceName: 'friend',
  pageName: 'friend.listDetail',
  headingResourceName: 'friend_list',
  headingResourceKey: 'list_id',
  headingLabelMessage: 'friend_list_title',
  breadcrumb: false,
  backPage: false,
  loginRequired: true
});
