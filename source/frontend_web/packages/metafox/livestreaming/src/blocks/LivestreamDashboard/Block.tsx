/**
 * @type: block
 * name: livestreaming.block.livestreamView
 * title: livestream Detail
 * keywords: livestream
 * description: Display livestream detail
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
