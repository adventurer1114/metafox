/**
 * @type: block
 * name: advertise.block.sponsorship
 * title: Sponsorship advertise
 * keywords: advertise
 * description: Display sponsorship advertise
 * thumbnail:
 */

import { createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

export default createBlock<Props>({
  extendBlock: Base
});
