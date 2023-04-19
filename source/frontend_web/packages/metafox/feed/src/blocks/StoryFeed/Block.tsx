/**
 * @type: block
 * name: feed.block.story
 * title: Feed Story
 * keywords: feed
 * description:
 * thumbnail:
 */

import { createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

export default createBlock<Props>({
  extendBlock: Base,
  name: 'Story',
  overrides: {
    blockProps: { noFooter: true, noHeader: true }
  },
  defaults: {
    title: 'Story',
    blockProps: { variant: 'contained', marginBottom: '2' }
  }
});
