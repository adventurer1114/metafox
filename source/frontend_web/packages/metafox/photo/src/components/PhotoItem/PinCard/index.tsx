/**
 * @type: itemView
 * name: photo.itemView.pinCard
 */
import {
  actionCreators,
  connectItemView
} from '@metafox/photo/hocs/connectPhoto';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators, {
  tags: true,
  categories: true
});
