/**
 * @type: block
 * name: event.block.co-hostInvitation
 * title: Event Host Detail
 * keywords: event
 * description: Display event detail
 */

import { connectSubject, createBlock } from '@metafox/framework';
import {
  actionCreators,
  connectItemView
} from '@metafox/event/hocs/connectInviteItem';
import Base from './Base';

const Enhance = connectSubject(connectItemView(Base, actionCreators));

export default createBlock<any>({
  extendBlock: Enhance
});
