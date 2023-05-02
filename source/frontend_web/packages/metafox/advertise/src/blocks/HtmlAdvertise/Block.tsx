/**
 * @type: block
 * name: advertise.block.html
 * title: Html advertise
 * keywords: advertise, html
 * description: Display advertise html
 * thumbnail:
 */

import { createBlock } from '@metafox/framework';
import Base, { Props } from './Base';

export default createBlock<Props>({
  name: 'AdvertiseBannerBlock',

  extendBlock: Base,
  defaults: {
    title: 'Advertise Html',
    blockLayout: 'Advertise Html'
  }
});
