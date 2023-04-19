/**
 * @type: theme
 * name: a0
 * system: true
 * bundle: web
 */
import blockLayouts from './layout.blocks.json';
import gridLayouts from './layout.grids.json';
import itemLayouts from './layout.items.json';
import noContentLayouts from './layout.noContents.json';
import templates from './layout.templates.json';
import pageLayouts from './layout.pages.json';
import siteBlocks from './layout.siteBlocks.json';
import styles from './styles.json';
import processors from './processors';

const config = {
  siteBlocks,
  blockLayouts,
  gridLayouts,
  itemLayouts,
  noContentLayouts,
  pageLayouts,
  templates,
  processors,
  styles
};

export default config;
