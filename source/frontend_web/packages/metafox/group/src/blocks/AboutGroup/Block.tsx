/**
 * @type: block
 * name: group.settings.about
 * title: Group Setting About
 * keywords: group
 * description: Group Setting About
 * thumbnail:
 */
import {
  connect,
  connectSubject,
  createBlock,
  GlobalState
} from '@metafox/framework';
import Base from './Base';

const Enhancer = connect((state: GlobalState) => ({
  data: state._actions.group.group.getGroupAboutForm
}))(connectSubject(Base));

export default createBlock<any>({
  extendBlock: Enhancer,
  defaults: {
    blockLayout: 'Main Form'
  }
});
