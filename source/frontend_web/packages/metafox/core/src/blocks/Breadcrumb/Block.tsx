/**
 * @type: block
 * name: core.breadcrumbBlock
 * title: Breadcrumb
 */

import { connectAppUI, createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

const Enhance = connectAppUI(Base);

export default createBlock<Props>({
  extendBlock: Enhance,
  defaults: {
    blockLayout: 'main breadcrumb'
  }
});
