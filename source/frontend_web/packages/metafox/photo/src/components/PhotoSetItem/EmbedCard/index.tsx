/**
 * @type: embedView
 * name: photo_set.embedItem.insideFeedItem
 */
import {
  actionCreators,
  connectItemView
} from '@metafox/photo/hocs/connectPhoto';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators, { photos: true });
