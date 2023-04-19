/**
 * @type: route
 * name: groups.manageMyContent
 * path: /group/:id/review_my_content/:tab?
 * chunkName: pages.group
 * bundle: web
 */
import { createMultiTabPage } from '@metafox/framework';

export default createMultiTabPage({
  appName: 'group',
  resourceName: 'group',
  pageName: 'groups.manageMyContent',
  defaultTab: 'pending',
  tabs: {
    pending: 'group.manage.myPendingPosts',
    published: 'group.manage.myPublishedPosts',
    declined: 'group.manage.myDeclinedPosts',
    removed: 'group.manage.myRemovedPosts'
  },
  loginRequired: true,
  heading: 'your_content',
  conditionChangeDefaultTab: [
    'or',
    ['truthy', 'item.is_admin'],
    [
      'and',
      ['truthy', 'item.is_moderator'],
      ['truthy', 'item.extra.can_manage_pending_posts']
    ]
  ],
  defaultTabChange: 'published'
});
