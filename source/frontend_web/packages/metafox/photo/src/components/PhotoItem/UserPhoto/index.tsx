/**
 * @type: embedView
 * name: user_photo.embedItem.insideFeedItem
 */
import {
  actionCreators,
  connectItemView
} from '@metafox/photo/hocs/connectPhoto';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators, { photo: true });
