/**
 * @type: block
 * name: group.settings.info
 * title: Group Setting Info
 * keywords: group
 * description: Group Setting Info
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
  data: state._actions.group.group.getGroupInfoForm
}))(connectSubject(Base));

export default createBlock<any>({
  extendBlock: Enhancer,
  defaults: {
    blockLayout: 'Main Form'
  }
});
