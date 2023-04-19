/**
 * @type: ui
 * name: statusComposer.plugin.hashtag
 * lazy: false
 */

import createHashtagPlugin from '@draft-js-plugins/hashtag';

export default function HashtagCreator(plugins: any[]) {
  const hashtagPlugin = createHashtagPlugin({
    theme: { hashtag: 'hashtagStyle' }
  });

  plugins.push(hashtagPlugin);
}
