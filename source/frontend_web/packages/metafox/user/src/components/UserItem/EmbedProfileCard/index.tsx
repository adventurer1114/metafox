/**
 * @type: embedView
 * name: user_profile.embedItem.insideFeedItem
 */
import { connectItemView } from '../../../hocs/connectUserProfileItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, () => {});
