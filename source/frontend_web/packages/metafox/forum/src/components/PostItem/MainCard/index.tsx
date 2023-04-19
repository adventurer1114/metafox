/**
 * @type: itemView
 * name: forum_post.itemView.mainCard
 */

import {
  actionCreators,
  connectItemView
} from '@metafox/forum/hocs/connectForumPost';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
