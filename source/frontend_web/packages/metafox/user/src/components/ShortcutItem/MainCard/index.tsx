/**
 * @type: itemView
 * name: shortcut.itemView.mainCard
 */
import {
  actionCreators,
  connectItemView
} from '@metafox/user/hocs/connectShortcutItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
