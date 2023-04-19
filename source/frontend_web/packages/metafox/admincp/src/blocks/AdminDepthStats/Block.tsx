/**
 * @type: block
 * name: core.block.AdminDepthStats
 * title: AdminCP - Depth Statistics
 * bundle: admincp
 * admincp: true
 */
import { createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

export default createBlock<Props>({
  extendBlock: Base,
  defaults: {
    title: 'In-Depth Statistics',
    blockLayout: 'Admin - Contained'
  }
});
