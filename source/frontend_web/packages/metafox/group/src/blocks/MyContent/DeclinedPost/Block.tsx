/**
 * @type: block
 * name: group.manage.myDeclinedPosts
 * title: Manager My Declined Posts
 * keywords: group
 * description: Manager My Declined Posts
 * thumbnail:
 */

import { createBlock } from '@metafox/framework';
import Base from './Base';

export default createBlock<any>({
  name: 'MyGroupDeclinedPosts',
  extendBlock: Base,
  overrides: {
    title: 'Declined',
    itemView: 'group.itemView.myDeclinedPost',
    blockLayout: 'App Lists Pending Posts',
    gridLayout: 'Group - PendingPost - Main Card',
    itemLayout: 'Group - PendingPost - Main Card',
    contentType: 'group'
  }
});
