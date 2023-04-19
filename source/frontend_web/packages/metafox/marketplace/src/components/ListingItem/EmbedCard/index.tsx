/**
 * @type: embedView
 * name: marketplace.embedItem.insideFeedItem
 */
import {
  actionCreators,
  connectItemView
} from '@metafox/marketplace/hocs/connectMarketplaceItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
