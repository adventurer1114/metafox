/**
 * @type: block
 * name: pages.settings.about
 * title: Page Setting About
 * keywords: page
 * description: Page Setting About
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
  data: state._actions.page.page.getPageAboutForm
}))(connectSubject(Base));

export default createBlock<any>({
  extendBlock: Enhancer,
  defaults: {
    blockLayout: 'Main Form'
  }
});
