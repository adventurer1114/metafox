/**
 * @type: block
 * name: group.block.groupReviewProfileHeader
 * title: Group Review Profile Header
 * keywords: group, profile
 * description: Display large header on review group's profile.
 * thumbnail:
 */
import { createBlock } from '@metafox/framework';
import Base from './Base';

export default createBlock({
  name: 'GroupReviewProfileHeaderBlock',
  extendBlock: Base
});
