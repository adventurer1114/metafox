/**
 * @type: theme
 * name: installation
 * system: true
 * bundle: installation
 */
import blockLayouts from './layout.blocks.json';
import gridLayouts from './layout.grids.json';
import itemLayouts from './layout.items.json';
import noContentLayouts from './layout.noContents.json';
import templates from './layout.templates.json';
import pageLayouts from './layout.pages.json';
import styles from './styles.json';
import processors from '@metafox/theme-default/processors';

const config = {
  blockLayouts,
  gridLayouts,
  itemLayouts,
  noContentLayouts,
  pageLayouts,
  templates,
  styles,
  processors
};

export default config;
