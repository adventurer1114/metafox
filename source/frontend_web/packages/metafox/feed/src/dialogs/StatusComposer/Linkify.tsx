/**
 * @type: ui
 * name: statusComposer.plugin.linkify
 * lazy: false
 */

import createLinkifyPlugin from '@draft-js-plugins/linkify';

export default function HashtagCreator(plugins: any[]) {
  const linkifylugin = createLinkifyPlugin();

  plugins.push(linkifylugin);
}
