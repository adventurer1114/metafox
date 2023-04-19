/**
 * @type: block
 * name: group.settings.permission
 * title: Group Permission Settings
 * keywords: group
 * description: Group Permission Settings
 * thumbnail:
 */
import { createBlock } from '@metafox/framework';
import Base from './Base';

export default createBlock<any>({
  extendBlock: Base,
  defaults: {
    blockLayout: 'Main Form'
  }
});
