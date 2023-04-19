/**
 * @type: block
 * name: video.block.videoView
 * title: Video Detail
 * keywords: video
 * description: Display video detail
 */

import { connectSubject, createBlock } from '@metafox/framework';
import connectVideoItem from '../../containers/connectVideoItem';
import Base, { Props } from './Base';

const Enhance = connectSubject(connectVideoItem(Base));

export default createBlock<Props>({
  extendBlock: Enhance,
  defaults: {
    placeholder: 'Search',
    blockProps: {
      variant: 'plained',
      titleComponent: 'h2',
      titleVariant: 'subtitle1',
      titleColor: 'textPrimary',
      noFooter: true,
      noHeader: true,
      blockStyle: {},
      contentStyle: {
        borderRadius: 'base'
      },
      headerStyle: {},
      footerStyle: {}
    }
  }
});
