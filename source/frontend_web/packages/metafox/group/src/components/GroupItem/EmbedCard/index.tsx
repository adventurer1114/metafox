/**
 * @type: embedView
 * name: group.embedItem.insideFeedItem
 */
import {
  actionCreators,
  connectItemView
} from '../../../hocs/connectGroupItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
