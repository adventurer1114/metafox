/**
 * @type: block
 * name: livestreaming.block.commentOffline
 * title: livestream offline comment
 * keywords: livestream
 * description: Display livestream offline comment
 */

import { connectSubject, createBlock } from '@metafox/framework';
import {
  actionCreators,
  connectItemView
} from '../../hocs/connectLivestreamItem';
import Base from './Base';

const Enhance = connectSubject(connectItemView(Base, actionCreators));

export default createBlock({
  extendBlock: Enhance
});
