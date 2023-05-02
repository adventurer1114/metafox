/**
 * @type: block
 * name: advertise.block.allAdsMobile
 * title: All advertise mobile
 * keywords: advertise
 * description: Display all my advertises mobile
 * thumbnail:
 */

import { createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

export default createBlock<Props>({
  extendBlock: Base
});
