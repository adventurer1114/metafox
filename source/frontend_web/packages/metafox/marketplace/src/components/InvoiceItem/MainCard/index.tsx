/**
 * @type: itemView
 * name: marketplace_invoice.itemView.mainCard
 */

import {
  connectItemView,
  actionCreators
} from '@metafox/marketplace/hocs/connectInvoice';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
