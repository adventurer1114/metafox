/**
 * @type: block
 * name: marketplace.block.pagelistingBlock
 * title: Page's Marketplace Listings
 * keywords: marketplace
 * description:
 * thumbnail:
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';

const MarketplaceListingBlock = createBlock<ListViewBlockProps>({
  name: 'MarketplaceListingBlock',
  extendBlock: 'core.block.listview',
  overrides: {
    contentType: 'marketplace'
  },
  defaults: {
    title: 'Marketplace Listings',
    blockProps: { variant: 'contained' },
    itemView: 'marketplace.itemView.mainCard',
    gridContainerProps: { spacing: 2 },
    gridItemProps: { xs: 12, sm: 6, md: 4, lg: 3, xl: 2 },
    canLoadMore: true,
    dataSource: {
      apiUrl: '/marketplace',
      apiParams: 'user_id=:id&limit=6'
    }
  }
});

export default MarketplaceListingBlock;
