/**
 * @type: block
 * name: pages.settings.info
 * title: Pages Setting Info
 * keywords: page
 * description: Pages Setting Info
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
  data: state._actions.page.page.getPageInfoForm
}))(connectSubject(Base));

export default createBlock<any>({
  extendBlock: Enhancer,
  defaults: {
    blockLayout: 'Main Form'
  }
});
