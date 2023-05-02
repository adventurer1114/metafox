/**
 * @type: block
 * name: livestreaming.block.commentLive
 * title: livestream detail comment
 * keywords: livestream
 * description: Display livestream detail
 */

import { connectSubject, createBlock } from '@metafox/framework';
import Base from './Base';

const Enhance = connectSubject(Base);

export default createBlock({
  extendBlock: Enhance,
  defaults: {
    blockLayout: 'LiveStreaming Comment'
  }
});
