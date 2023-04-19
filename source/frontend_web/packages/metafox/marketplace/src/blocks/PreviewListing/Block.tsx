/**
 * @type: block
 * name: listing.block.listingPreview
 * title: Listing Detail Preview
 * keywords: photo
 * description: Display listing detail
 */

import { createBlock } from '@metafox/framework';
import { MarketplaceDetailViewProps } from '@metafox/marketplace/types';
import Base from './Base';

export default createBlock<MarketplaceDetailViewProps>({
  extendBlock: Base,
  defaults: {
    blockProps: {
      variant: 'plained',
      titleComponent: 'h2',
      titleVariant: 'subtitle1',
      titleColor: 'textPrimary',
      noFooter: true,
      noHeader: true,
      blockStyle: {},
      contentStyle: {},
      headerStyle: {},
      footerStyle: {}
    }
  }
});
