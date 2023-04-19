/**
 * @type: block
 * name: core.block.mainBlock
 * title: Main Block
 * keywords: general
 */
import { createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

export default createBlock<Props>({
  extendBlock: Base,
  name: 'MainBlock'
});
