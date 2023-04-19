const { Workbox } = require('../workbox');
const fs = require('fs');
const path = require('path');
const log = require('../helpers/log');
const paths = require('../config/paths');
const glob = require('glob');
const getCommentedInfo = require('../helpers/getCommentedInfo');
const os = require('os');
const pathToReg = require('path-to-regexp');
const SILENT = !!process.env.MFOX_BUILD_SERVICE;

const {
  isPlainObject,
  trim,
  uniqBy,
  filter,
  flatten,
  get,
  isArray,
  sortBy
} = require('lodash');

const basePackages = {
  '@metafox/framework': 'local',
  '@metafox/local-store': 'local',
  '@metafox/cookie': 'local',
  '@metafox/toast': 'local',
  '@metafox/json2yup': 'local',
  '@metafox/constants': 'local',
  '@metafox/dialog': 'local',
  '@metafox/form': 'local',
  '@metafox/form-elements': 'local',
  '@metafox/html-viewer': 'local',
  '@metafox/intl': 'local',
  '@metafox/ui': 'local',
  '@metafox/jsx': 'local',
  '@metafox/layout': 'local',
  '@metafox/normalization': 'local',
  '@metafox/rest-client': 'local',
  '@metafox/route': 'local',
  '@metafox/theme-default': 'local',
  '@metafox/admincp': 'local',
  '@metafox/utils': 'local',
  '@metafox/echo': 'local'
};

const validViewTypes = [
  'block',
  'itemView',
  'dialog',
  'skeleton',
  'siteDock',
  'ui',
  'embedView',
  'formElement',
  'popover'
];

const validTypes = [
  ...validViewTypes,
  'themeProcessor',
  'route',
  'modalRoute',
  'saga',
  'theme',
  'reducer',
  'message',
  'service',
  'packages',
  'dependency',
  'layoutBlockFeature',
  'admincp.message',
  'mockService',
  'theme.style.editor',
  'theme.styles'
];

// loadable by type component.
const loadableByTypeMap = {
  block: true,
  skeleton: true,
  itemView: false,
  dialog: true,
  ui: true,
  embedView: true,
  formElement: true,
  route: true,
  modalRoute: false,
  siteDock: true,
  saga: false,
  reducer: false,
  service: false,
  message: false,
  package: false,
  dependency: false,
  'theme.style.editor': true // don't be loadable lib.
};
const chunkByTypeMap = {
  dialog: 'dialogs',
  route: 'routes',
  siteDock: 'boot',
  modalRoute: 'modalRoutes'
};

function getPackageDir(packageName) {
  try {
    return path.dirname(require.resolve(`${packageName}/package.json`));
  } catch (error) {
    return null;
  }
}

const trimRoot = str => str.substring(Workbox.getRootDir().length + 1);

function mixFrom(packageName, file) {
  return `${packageName}/${file.replace(/.\w+$/, '')}`;
}

function mixSource(packageDir, file) {
  return path.resolve(packageDir, 'src', file);
}

function isValidType(type) {
  if (!type) return false;
  return validTypes.includes(type);
}

function bundleSinglePackage(bundle, packageName, dir, bundleType) {
  if (!SILENT) log.info('Analyzing', packageName);

  function checkScriptFile(filename) {
    const source = mixSource(dir, filename);
    const info = getCommentedInfo(source);
    if (info && isValidType(info['@type'])) {
      const type = info['@type'];
      delete info['@type'];

      bundle.push({
        info,
        from: mixFrom(packageName, filename),
        source,
        packageName,
        type
      });
    }
  }

  /**
   * @check layout file
   */
  function checkLayoutFile(filename) {
    const source = mixSource(dir, filename);
    let info = {};

    const data = JSON.parse(fs.readFileSync(source, { encoding: 'utf-8' }));

    if (Object.values(data).length > 1) {
      throw new Error(
        `Failed checking ${source}, it can contains single page configuration`
      );
    }

    Object.values(data).forEach(values => {
      if (!get(values, 'info.bundle')) {
        throw new Error(`Missing info.bundle in ${source}`);
      }
      info = values.info;
    });

    // console.log(chalk.cyan('found layout ' + outputPath(source)));

    bundle.push({
      source,
      packageName,
      type: 'layout',
      info
    });
  }

  /**
   * @check layout file
   */
  function checkTemplateFile(filename) {
    const source = mixSource(dir, filename);
    bundle.push({
      source,
      packageName,
      type: 'template'
    });
  }

  glob.sync('**/*.ts', { cwd: `${dir}/src` }).forEach(checkScriptFile);
  glob.sync('**/*.tsx', { cwd: `${dir}/src` }).forEach(checkScriptFile);
  glob
    .sync(`**/assets/pages/*.json`, {
      cwd: `${dir}/src`
    })
    .forEach(checkLayoutFile);

  glob
    .sync('**/templates.json', { cwd: `${dir}/src` })
    .forEach(checkTemplateFile);

  // check file name
  const messages = mixSource(dir, 'messages.json');

  if (fs.existsSync(messages)) {
    bundle.push({
      source: messages,
      packageName,
      type: 'message',
      info: {}
    });
  }

  const adminMessages = mixSource(dir, 'admincp.messages.json');

  if (fs.existsSync(adminMessages)) {
    bundle.push({
      source: adminMessages,
      packageName,
      type: 'message',
      info: {
        bundle: 'admincp'
      }
    });
  }
}

function capitalizeWord(s) {
  return s.substring(0, 1).toUpperCase() + s.substring(1);
}

function capitalizeCase(name) {
  return name
    ? name.replace('_', '.').split(/\W+/g).map(capitalizeWord).join('')
    : 'undefinedName';
}

function camelCase(name) {
  return name
    .replace('_', '.')
    .split(/\W+/g)
    .map((x, index) => (index > 0 ? capitalizeWord(x) : x))
    .join('');
}

function fixRoutePath(path) {
  // console.log('testPath', path);
  const arr = path
    .split(',')
    .map(x => x.trim())
    .filter(Boolean)
    .filter(path => pathToReg.match(path))
    .map(x => '/' + x.replace(/(^\/|\/$)/g, ''));
  return arr.length > 1 ? arr : arr.pop();
}

function fixBooleanValue(end, defaultValue) {
  switch (end) {
    case '0':
      return false;
    case 'false':
      return false;
    case 'no':
      return false;
    default:
      return defaultValue;
  }
}

function fixRoutePriority(priority, index) {
  const x = priority ? parseInt(priority, 10) : index;
  return x != NaN ? x : index;
}

function bundleAllPackages(bundle, packages, bundleType) {
  packages.forEach(packageName => {
    const packageDir = getPackageDir(packageName);

    if (!packageDir) return;
    bundleSinglePackage(bundle, packageName, packageDir, bundleType);
  });
}

function outputPath(fullPath) {
  const rootDir = Workbox.getRootDir();
  return '.' + fullPath.substring(rootDir.length);
}

/**
 * Chunk all theme resource to single one.
 * Theme must be recovery in to single one.
 * @param stats
 * @param pages
 * @param bundle
 * @param dirname
 * @param bundleType
 */
function chunkThemes(stats, pages, bundle, dirname, bundleType) {
  const themes = bundle.filter(
    x => x.type === 'theme' && x.info.bundle == bundleType
  );

  stats.themes = Object.keys(themes).length;

  themes.forEach(theme => {
    const dir = path.dirname(theme.source).substring(process.cwd().length + 1);
    const { name } = theme.info;

    const info = Object.assign({}, theme.info, { id: name, dir });

    const source = `/* eslint-disable */
import theme from '${theme.from}';

export const info = ${JSON.stringify(info)};

export default theme;
    `;
    // console.log('chunk theme package', theme.packageName);
    const destination = path.resolve(dirname, `theme.${name}.tsx`);

    writeToFile(destination, source);
  });
}

/**
 * Chunk all theme resource to single one.
 * Theme must be recovery in to single one.
 * @param stats
 * @param pages
 * @param bundle
 * @param dirname
 * @param bundleType
 */
function chunkThemeStyles(stats, pages, bundle, dirname, bundleType) {
  const themeStyles = bundle
    .filter(x => x.type === 'theme.styles')
    .filter(x => !x.info.bundle || x.info.bundle === bundleType);

  stats.styles = Object.keys(themeStyles).length;

  themeStyles.forEach(style => {
    const dir = path.dirname(style.source).substring(process.cwd().length + 1);
    const { name, theme } = style.info;
    const id = theme + ':' + name;
    const info = Object.assign({}, style.info, { id, dir });

    const source = `/* eslint-disable */

import style from '${style.from}';

export const info = ${JSON.stringify(info)};

export default style;
    `;
    // console.log('chunk theme package', theme.packageName);

    const destination = path.resolve(
      dirname,
      `style.${name}.theme.${theme}.tsx`
    );

    writeToFile(destination, source);
  });
}

function chunkMessages(stats, bundle, destination, bundleType = false) {
  let data = {};

  bundle
    .filter(x => x.type === 'message')
    .forEach(x => {
      data = Object.assign(data, require(x.source));
    });

  if (bundleType) {
    bundle
      .filter(x => x.type === 'message')
      .filter(x => x.info.bundle === undefined || x.info.bundle === bundleType)
      .forEach(x => {
        data = Object.assign(data, require(x.source));
      });
  }

  stats.messages = Object.keys(data).length;

  writeToFile(destination, data);
}

function chunkMessageJs(destination) {
  const source = filterChunkedOutput(`
import messages from './messages.json';

  ${injectConfigSource('messages')}`);

  writeToFile(destination, source);
}

function discoverElements(name, types, stats, bundle, basePath) {
  const items = collectBundleItems(bundle, types, capitalizeCase);

  const blockFeatures = toLoadableObject(name, items, x => `[${x.importName}]`);

  stats[name] = items.length;

  const source = `${filterChunkedOutput(blockFeatures)}
// inject to config
export default ${name};
`;

  const destination = path.resolve(basePath, name + '.ts');

  writeToFile(destination, source);
}

function toLoadableJSON(items, processItem) {
  return items.reduce((acc, x) => {
    const obj = processItem(x);
    if (obj) acc[x.info.name] = obj;
    return acc;
  }, {});
}

function chunkPageLayouts(bundle, bundleType) {
  let pageFileMap = {};
  let pages = {};
  // write down to /dev0/pageFileMap

  bundle
    .filter(x => x.type === 'layout')
    .filter(x => x.info.bundle === bundleType)
    .forEach(x => {
      const filename = trimRoot(x.source);
      const config = require(x.source);
      Object.keys(config).forEach(page => {
        pageFileMap[page] = filename;
      });
      pages = Object.assign(pages, config);
    });

  writeToFile(Workbox.getRootDir() + '/scripts/pages.map.json', pageFileMap);

  return pages;
}

function chunkAllLayouts(stats, pages, bundle, destination, bundleType) {
  let siteDock = {};

  bundle
    .filter(x => x.type === 'siteDock')
    .filter(x => x.info.bundle === undefined || x.info.bundle === bundleType)
    .forEach(x => {
      siteDock[x.info.name] = true;
    });

  const data = {};
  data.pages = pages;
  data.siteDock = Object.keys(siteDock);
  stats.layouts = Object.keys(pages).length;
  stats.siteDock = Object.keys(siteDock).length;

  // skip overload

  const source = filterChunkedOutput(`
const layouts  =  ${JSON.stringify(data, null, '  ')};

  ${injectConfigSource('layouts')}
  `);

  writeToFile(destination, source);
}

function normalizeBlockInfo({ info }) {
  if (!info.title) return false;

  const obj = {
    title: info.title,
    description: info.description ?? '',
    keywords: info.keywords
      ? info.keywords.split(',').map(trim).filter(Boolean)
      : [],
    bundleType: info.bundle
  };

  return obj;
}

function chunkBlockInfo(stats, bundle, destination, bundleType) {
  const items = collectBundleItems(bundle, ['block'], capitalizeCase);

  const blocks = toLoadableJSON(items, x => ({
    ...x.info,
    admincp: x.info.admincp ?? x.info.bundle === 'admincp'
  }));

  // skip overload

  const source = filterChunkedOutput(`
import blockFeatures from './blockFeatures';

  const info = {
    blocks: ${JSON.stringify(blocks, null, '  ')},
    blockFeatures,
  }
  
  export default info;
`);

  writeToFile(destination, source);
}

/**
 * @example injectConfigSource('routes', 'models')
 * @param {string} parameters
 * @return string
 */
function injectConfigSource(parameters) {
  const body = parameters
    .split(',')
    .map(name => name.trim())
    .map(name => `config.${name}=${name};`)
    .join('\n    ');
  return `export default function injector(config: any) {
    ${body}
}`;
}

function prepareImports(items) {
  return items
    .map(({ info, type, from, importName }) => {
      // should loadable or chunk?
      const lazy =
        (!!loadableByTypeMap[type] || info.chunkName) && info.lazy != false;

      const chunkName = info.chunkName ? info.chunkName : chunkByTypeMap[type];
      const chunkSyntax = chunkName
        ? `/* webpackChunkName: "${chunkName}" */`
        : '';

      if (lazy) {
        return `
const ${importName} = loadable(() => import(${chunkSyntax} '${from}'));`;
      }

      return `import ${importName} from '${from}';`;
    })
    .join('\n');
}

function filterChunkedOutput(content) {
  if (content.indexOf('loadable(') > 0 || content.indexOf('React.lazy') > 0) {
    content = `import loadable from '@loadable/component'; 

${content}`;
  }

  return `/* eslint-disable */
${content}`;
}

function toLoadableArray(exportName, items, processItem) {
  const processItems = flatten(items.map(processItem));

  let content = JSON.stringify(processItems, null, '  ');

  let imports = prepareImports(items);

  items.forEach(({ importName }) => {
    content = content
      .replaceAll(`"[${importName}]"`, importName)
      .replaceAll(`"<[${importName}] />"`, `<${importName} />`);
  });

  return `${imports} 
   const ${exportName} = ${content};`;
}

function toLoadableObject(exportName, items, processItem) {
  let data = JSON.stringify(
    uniqBy(items, 'info.name').reduce((acc, x) => {
      acc[x.info.name] = processItem(x);
      return acc;
    }, {}),
    null,
    '  '
  );

  let imports = prepareImports(uniqBy(items, 'info.name'));

  items.forEach(({ importName }) => {
    data = data.replace(`"[${importName}]"`, importName);
  });

  return `${imports} 

// export 
export const ${exportName} = ${data};`;
}

function collectBundleItems(
  bundle,
  includeTypes,
  naming,
  bundleType,
  callback
) {
  return Object.values(
    bundle
      .filter(x => includeTypes.includes(x.type))
      .filter(
        x =>
          x.info.bundle === undefined ||
          bundleType === undefined ||
          x.info.bundle === bundleType
      )
      .reduce((acc, x) => {
        x.importName = naming(x.info.name);
        acc[x.info.name] = x;
        return acc;
      }, {})
  )
    .map((x, index) => ({
      ...x,
      priority: fixRoutePriority(x.info.priority, index)
    }))
    .filter(callback ?? Boolean)
    .sort((a, b) => a.priority - b.priority);
}

function chunkRoutes(stats, bundle, destination, bundleType) {
  const processItem = ({ info: { name, path, end }, importName }) => {
    const fixedPath = fixRoutePath(path);
    if (isArray(fixedPath)) {
      return fixedPath.map((p, index) => ({
        component: `[${importName}]`,
        path: p,
        name
      }));
    }
    return {
      component: `[${importName}]`,
      path: fixedPath,
      name
    };
  };

  const popoverHandlers = {};

  bundle
    .filter(x => x.type === 'popover') // skip popover
    .filter(x => x.info.path)
    .forEach(x => {
      popoverHandlers[x.info.name] = {
        component: x.info.name,
        path: fixRoutePath(x.info.path)
      };
    });

  const modalItems = collectBundleItems(
    bundle,
    ['modalRoute'],
    capitalizeCase,
    bundleType
  );

  let routeItems = collectBundleItems(
    bundle,
    ['route'],
    capitalizeCase,
    bundleType
  );

  stats.routes = routeItems.length;

  stats.modals = modalItems.length;

  const routes = toLoadableArray('routes', routeItems, processItem);

  const modals = toLoadableArray('modals', modalItems, processItem);

  const source = filterChunkedOutput(`
/* export routes */
${routes}

/* export modal */
${modals}

/* export popoverHandlers*/
const popoverHandlers  = ${JSON.stringify(
    Object.values(popoverHandlers),
    null,
    '  '
  )}
  ${injectConfigSource('routes, modals, popoverHandlers')}`);

  writeToFile(destination, source);
}

function chunkAllViews(stats, bundle, destination, bundleType) {
  const viewItems = collectBundleItems(
    bundle,
    validViewTypes,
    capitalizeCase,
    bundleType
  );

  const views = toLoadableObject('views', viewItems, x => `[${x.importName}]`);

  stats.views = viewItems.length;

  const source = `${filterChunkedOutput(views)}
// inject to config
${injectConfigSource('views')}`;

  writeToFile(destination, source);
}

function chunkAllServices(stats, bundle, destination) {
  const named = 'services';
  const serviceItems = collectBundleItems(bundle, ['service'], capitalizeCase);
  const services = toLoadableObject(
    named,
    serviceItems,
    x => `[${x.importName}]`
  );

  stats.services = serviceItems.length;
  const source = `
${filterChunkedOutput(services)}

// inject to config
${injectConfigSource(named)}`;

  writeToFile(destination, source);
}

function chunkAllMockedService(stats, bundle, destination) {
  const named = 'mockServices';

  const mainServices = collectBundleItems(
    bundle,
    ['service'],
    capitalizeCase,
    undefined
  );

  const mockServices = collectBundleItems(
    bundle,
    ['mockService'],
    capitalizeCase
  ).concat(mainServices);

  const services = toLoadableObject(
    named,
    mockServices,
    x => `[${x.importName}]`
  );

  stats.services = mockServices.length;
  const source = `
// THIS IS DEFAULT SERVICE FOR RUN TESTING.
// PLEASE DO NOT IMPORT INTO OTHER PLACE.

${filterChunkedOutput(services)}

// inject to config
export default ${named}
`;

  writeToFile(destination, source);
}

function chunkProduce(stats, bundle, destination, bundleType) {
  // remove duplicated.

  const sagaItems = collectBundleItems(
    bundle,
    ['saga'],
    name => camelCase(name) + 'Saga',
    bundleType
  );
  const reducerItems = collectBundleItems(
    bundle,
    ['reducer'],
    name => camelCase(name) + 'Reducer',
    bundleType
  );
  const sagas = toLoadableArray('sagas', sagaItems, x => `[${x.importName}]`);

  const reducers = toLoadableObject(
    'reducers',
    reducerItems,
    x => `[${x.importName}]`
  );

  const source = filterChunkedOutput(`
  ${sagas}
  ${reducers}
  ${injectConfigSource('reducers,sagas')}
  `);

  stats.reducers = reducerItems.length;
  stats.sagas = sagaItems.length;

  writeToFile(destination, source);
}

function writeToFile(filename, source) {
  const content = isPlainObject(source)
    ? JSON.stringify(source, null, '  ')
    : source;

  log.info('Updated', outputPath(filename));

  fs.writeFileSync(filename, content + os.EOL, { encoding: 'utf-8' });
}

function chunkTemplateJson(bundle, dest) {
  let map = {};
  let templates = {};
  // write down to /dev0/pageFileMap

  bundle
    .filter(x => x.type === 'template')
    .forEach(x => {
      const filename = trimRoot(x.source);
      const config = require(x.source);
      Object.keys(config).forEach(name => {
        map[name] = filename;
      });
      templates = Object.assign(templates, config);
    });

  writeToFile(Workbox.getRootDir() + '/scripts/templates.map.json', map);
  writeToFile(dest, templates);

  return templates;
}

function chunkSettingFile(origin, destination) {
  const config = JSON.parse(JSON.stringify(origin));
  delete config.packages;

  const source = `/* eslint-disable */
const root = ${JSON.stringify(config, null, '  ')}

// inject root
${injectConfigSource('root')}`;
  writeToFile(destination, source);
}

function chunkApp(name, bundle, filename) {
  const source = `
import $x2 from '${name}/${bundle}/settings';
import $x3 from '${name}/${bundle}/services';
import $x4 from '${name}/${bundle}/routes';
import $x5 from '${name}/${bundle}/message';
import $x6 from '${name}/${bundle}/views';
import $x7 from '${name}/${bundle}/produce';
import $x8 from '${name}/${bundle}/layouts';

const bundle = [$x2, $x3, $x4, $x5, $x6, $x7, $x8].reduce((acc, value) => {
  value(acc);

  return acc;
}, {});

export default bundle;
`;
  writeToFile(filename, source);
}

function chunkThemeSnippetEditor(bundle, destination, bundleType) {
  const processItem = ({ info, importName }) => {
    return {
      ...info,
      component: `[${importName}]`
    };
  };

  let items = collectBundleItems(
    bundle,
    ['theme.style.editor'],
    capitalizeCase,
    bundleType
  );

  let themeSnippets = toLoadableArray('themeSnippets', items, processItem);

  // reorder theme snippet

  const source = filterChunkedOutput(`
/* export themeSnippets */
${themeSnippets}

export default themeSnippets;
`);

  writeToFile(path.resolve(destination, 'theme.style.editor.tsx'), source);
}

function bundleFor({ siteName, bundleDir, bundleType }) {
  const mode = process.env.NODE_ENV || 'production';
  // console.log(chalk.green('Analyzing configuration ...'));

  const settingFile = paths.appSettingJson;
  const bundle = [];
  const rootDir = Workbox.getRootDir();
  const siteDir = path.resolve(rootDir, 'app');
  const settings = require(settingFile);
  const packages = { ...basePackages, ...settings.packages };
  const stats = {};

  const createPath = fileName =>
    path.resolve(rootDir, siteDir, bundleDir, fileName);

  const discoverPath = createPath('');

  if (!fs.existsSync(discoverPath)) {
    fs.mkdirSync(discoverPath);
  }

  bundleAllPackages(bundle, Object.keys(packages), bundleType);
  // summary stats

  discoverElements(
    'blockFeatures',
    ['layoutBlockFeature'],
    stats,
    bundle,
    discoverPath
  );

  const pages = chunkPageLayouts(bundle, bundleType, 'pages.json');

  chunkApp(
    siteName,
    `bundle-${bundleType}`,
    createPath('config.tsx'),
    bundleType
  );
  chunkMessages(stats, bundle, createPath('messages.json'), bundleType);
  chunkAllLayouts(stats, pages, bundle, createPath('layouts.tsx'), bundleType);
  chunkThemes(
    stats,
    pages,
    bundle,
    path.resolve(rootDir, siteDir, bundleDir),
    bundleType
  );
  chunkThemeStyles(
    stats,
    pages,
    bundle,
    path.resolve(rootDir, siteDir, bundleDir),
    bundleType
  );
  chunkThemeSnippetEditor(bundle, path.resolve(rootDir, siteDir, bundleDir));
  chunkBlockInfo(stats, bundle, createPath('blockInfo.tsx'), bundleType);
  chunkMessageJs(createPath('message.tsx'), bundleType);
  chunkRoutes(stats, bundle, createPath('routes.tsx'), bundleType);
  chunkAllViews(stats, bundle, createPath('views.tsx'), bundleType);
  chunkAllServices(stats, bundle, createPath('services.tsx'), bundleType);
  chunkAllMockedService(
    stats,
    bundle,
    createPath('mockServices.tsx'),
    bundleType
  );
  chunkProduce(stats, bundle, createPath('produce.tsx'), bundleType);
  chunkSettingFile(settings, createPath('settings.tsx'), bundleType);
  // process chunked language.
  // console.log(chalk.cyan(JSON.stringify(stats, null, '  ')));
}

module.exports = function buildBundle({ profile, type: bundleType }) {
  log.heading('UPDATE BUNDLE SETTING ' + process.env.MFOX_BUILD_TYPE);

  log.dump(paths);
  log.info('Proxy file:', paths.proxyJson);
  log.info('Env file:', paths.dotenv);
  log.info('Setting file:', paths.appSettingJson);

  if (!bundleType) {
    bundleType = process.env.MFOX_BUILD_TYPE ?? 'web';
  }

  const start = new Date().getTime();
  bundleFor({
    siteName: '@metafox/react-app',
    bundleDir: `src/bundle-${bundleType}`,
    bundleType
  });
  const spend = (new Date().getTime() - start) / 1000;
  log.info('Updated build settings in', spend, 'seconds');
};
