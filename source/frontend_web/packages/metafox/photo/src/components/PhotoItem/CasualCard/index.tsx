/**
 * @type: itemView
 * name: photo.itemView.casualCard
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
