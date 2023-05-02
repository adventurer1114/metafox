/**
 * @type: block
 * name: advertise.block.invoice
 * title: Invoice advertise
 * keywords: advertise
 * description: Display invoice advertise
 * thumbnail:
 */

import { createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

export default createBlock<Props>({
  extendBlock: Base
});
