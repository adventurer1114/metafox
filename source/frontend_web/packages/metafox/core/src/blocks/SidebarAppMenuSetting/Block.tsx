/**
 * @type: block
 * name: core.block.sidebarAppMenuSetting
 * keywords: sidebar
 * title: App Menu Setting
 */
import {
  connectItemView,
  connectSubject,
  createBlock
} from '@metafox/framework';
import Base, { Props } from './Base';

const Enhance = connectSubject(connectItemView(Base, () => {}));

export default createBlock<Props>({
  extendBlock: Enhance,
  defaults: {
    menuName: 'sidebarMenuSetting',
    blockLayout: 'sidebar app menu'
  }
});
