/**
 * @type: block
 * name: core.block.sidebarAppMenu
 * keywords: sidebar
 * title: App Menu
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
    menuName: 'sidebarMenu',
    blockLayout: 'sidebar app menu'
  }
});
