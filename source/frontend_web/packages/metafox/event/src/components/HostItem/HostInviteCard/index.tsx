/**
 * @type: itemView
 * name: event.itemView.hostInviteCard
 */

import inviteItemActions from '@metafox/event/actions/inviteItemActions';
import { connectItemView } from '@metafox/framework';
import ItemView from './ItemView';

export default connectItemView(ItemView, inviteItemActions);
