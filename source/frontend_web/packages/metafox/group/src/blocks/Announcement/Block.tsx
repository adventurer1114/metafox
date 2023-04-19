/**
 * @type: block
 * name: group.block.announcement
 * title: Announcement Group
 * keywords: announcement
 * description: Display announcement group
 */

import { createBlock, ListViewBlockProps } from '@metafox/framework';
import Base from './Base';

export default createBlock<ListViewBlockProps>({
  extendBlock: Base,
  name: 'AnnouncementListing',
  defaults: {
    gridLayout: 'Announcement - Slider',
    itemLayout: 'Announcement - Slider'
  },
  overrides: { showWhen: ['truthy', 'acl.announcement.announcement.view'] }
});
