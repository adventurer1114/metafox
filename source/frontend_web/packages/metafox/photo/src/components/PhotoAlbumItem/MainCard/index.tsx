/**
 * @type: itemView
 * name: photo_album.itemView.mainCard
 */
import {
  actionCreators,
  connectItemView
} from '@metafox/photo/hocs/connectPhotoAlbum';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators, {
  tags: true,
  categories: true
});
