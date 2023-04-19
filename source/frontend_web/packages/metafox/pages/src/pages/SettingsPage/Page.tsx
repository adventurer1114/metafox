/**
 * @type: route
 * name: pages.settings
 * path: /page/settings/:id(\d+)/:tab?
 * chunkName: pages.page
 * bundle: web
 */
import { createMultiTabPage } from '@metafox/framework';

export default createMultiTabPage({
  appName: 'page',
  resourceName: 'page',
  pageName: 'pages.settings',
  defaultTab: 'info',
  tabs: {
    info: 'pages.settings.info',
    about: 'pages.settings.about',
    permissions: 'pages.settings.permission',
    menu: 'core.block.comingSoon',
    chat: 'core.block.comingSoon'
  }
});
