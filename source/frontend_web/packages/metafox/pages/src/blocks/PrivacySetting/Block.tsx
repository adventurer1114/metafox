/**
 * @type: block
 * name: pages.settings.permission
 * title: Pages Permission Settings
 * keywords: page
 * description: Page Permission Settings
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
