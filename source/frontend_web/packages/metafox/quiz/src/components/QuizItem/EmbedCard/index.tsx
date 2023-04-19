/**
 * @type: embedView
 * name: quiz.embedItem.insideFeedItem
 */
import {
  actionCreators,
  connectItemView
} from '@metafox/quiz/hocs/connectQuizItem';
import ItemView from './ItemView';

export default connectItemView(ItemView, actionCreators);
