/**
 * @type: block
 * name: core.block.tabContainer
 * title: Tab Container
 * keywords: container
 * container: true
 */

import {
  connectItemView,
  connectSubject,
  createBlock
} from '@metafox/framework';
import { UIBlockViewProps } from '@metafox/ui';
import Base from './Base';

const Enhance = connectSubject(connectItemView(Base, () => {}));

export default createBlock<UIBlockViewProps>({
  extendBlock: Enhance,
  name: 'core.block.tabContainer',
  defaults: {
    title: 'Tab Container',
    blockProps: {
      blockStyle: {
        borderRadius: 'base'
      }
    }
  },
  overrides: {
    blockProps: {
      noFooter: true
    }
  },
  custom: {
    tabs: {
      name: 'tabProps.tabs',
      component: 'Text',
      label: 'Tabs',
      fullWidth: true,
      margin: 'normal',
      variant: 'outlined'
    },
    hasSearchBox: {
      name: 'hasSearchBox',
      component: 'Checkbox',
      label: 'Has Search Box?',
      margin: 'normal'
    }
  }
});
