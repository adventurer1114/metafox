/**
 * @type: block
 * name: photo.block.photoView
 * title: Photo Detail
 * keywords: photo
 * description: Display photo detail
 */

import { connectSubject, createBlock } from '@metafox/framework';
import {
  actionCreators,
  connectItemView
} from '@metafox/photo/hocs/connectPhoto';
import Base, { Props } from './Base';

const Enhance = connectSubject(
  connectItemView(Base, actionCreators, {
    tags: true,
    categories: true
  })
);

export default createBlock<Props>({
  extendBlock: Enhance,
  defaults: {
    blockProps: {
      variant: 'plained',
      titleComponent: 'h2',
      titleVariant: 'subtitle1',
      titleColor: 'textPrimary',
      noFooter: true,
      noHeader: true,
      blockStyle: {},
      contentStyle: {
        borderRadius: 'base',
        pt: 2,
        pb: 2
      },
      headerStyle: {},
      footerStyle: {}
    }
  }
});
