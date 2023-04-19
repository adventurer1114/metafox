/**
 * @type: itemView
 * name: photo_album.itemView.AddPhotoAlbumDetail
 */
import {
  actionCreators,
  connectItemView
} from '@metafox/photo/hocs/connectPhotoAlbum';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators, {});
