/**
 * @type: block
 * name: activity-point.block.package
 * keyword: activity point
 * title: Activity Points
 */

import activityPointActions from '@metafox/activity-point/actions/activityPointActions';
import {
  connectSubject,
  connectItemView,
  createBlock
} from '@metafox/framework';
import Base, { Props } from './Base';

const Enhance = connectSubject(connectItemView(Base, activityPointActions));

export default createBlock<Props>({
  extendBlock: Enhance
});
