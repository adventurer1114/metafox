/**
 * @type: block
 * name: event.block.groupEventOverviewBlock
 * title: Group's Event Overview
 * keywords: event
 * description: Display group's Overview.
 * profile: true
 */
import { createBlock, ListViewBlockProps } from '@metafox/framework';
import Base from './Base';

export default createBlock<ListViewBlockProps>({
  extendBlock: Base,
  defaults: {
    title: 'Upcoming Event'
  }
});
