/**
 * @type: route
 * name: groups.setting
 * path: /group/settings/:id(\d+)/:tab?
 * chunkName: pages.group
 * bundle: web
 */
import { createMultiTabPage } from '@metafox/framework';

export default createMultiTabPage({
  appName: 'group',
  resourceName: 'group',
  pageName: 'groups.settings',
  defaultTab: 'info',
  loginRequired: true,
  tabs: {
    info: 'group.settings.info',
    about: 'group.settings.about',
    permissions: 'group.settings.permission'
  }
});
