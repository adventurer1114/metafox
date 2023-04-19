/**
 * @type: block
 * name: quiz.block.quizView
 * title: Quiz Detail
 * keywords: quiz
 * description: Display quiz detail
 */

import { connectSubject, createBlock } from '@metafox/framework';
import { actionCreators, connectItemView } from '../../hocs/connectQuizItem';
import Base from './Base';

const Enhance = connectSubject(
  connectItemView(Base, actionCreators, {
    quiz_question: true,
    attachments: true
  })
);

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
        borderRadius: 'base',
        pt: 0,
        pb: 0
      },
      headerStyle: {},
      footerStyle: {}
    }
  }
});
