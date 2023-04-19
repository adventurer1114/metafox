/**
 * @type: block
 * name: event.block.guestViewEventBlock
 * title: Event Items
 * keywords: event
 * description: Display event upcoming items.
 */
import {
  connectItemView,
  connectSubject,
  createBlock,
  ListViewBlockProps
} from '@metafox/framework';
import Base from './Base';

const Enhance = connectSubject(connectItemView(Base, () => {}));

const GuestViewEventBlock = createBlock<ListViewBlockProps>({
  extendBlock: Enhance,
  overrides: {
    contentType: 'event'
  }
});

export default GuestViewEventBlock;
