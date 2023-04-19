/**
 * @type: block
 * name: core.block.AdminSiteStatus
 * title: AdminCP - Site Status
 * bundle: admincp
 * admincp: true
 */
import { createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

export default createBlock<Props>({
  extendBlock: Base,
  defaults: {
    title: 'Site Status',
    blockLayout: 'Admin - Contained'
  }
});
