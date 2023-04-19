/**
 * @type: embedView
 * name: event.embedItem.insideFeedItem
 */
import {
  actionCreators,
  connectItemView
} from '@metafox/event/hocs/connectEventItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
