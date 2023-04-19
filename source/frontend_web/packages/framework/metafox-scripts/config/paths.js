require('../config/env');
const path = require('path');
const fs = require('fs');
const getPublicUrlOrPath = require('react-dev-utils/getPublicUrlOrPath');

const isAdminCP = process.env.MFOX_BUILD_TYPE === 'admincp';
const isInstall = process.env.MFOX_BUILD_TYPE === 'installation';
const isDev = process.env.NODE_ENV !== 'production';
const workbox = require('../workbox/workbox');

const appDirectory = path.join(process.cwd(), 'app');
const resolveApp = relativePath => path.resolve(appDirectory, relativePath);

const appPackageJson = resolveApp('package.json');
// @risk:  could not build admin
// const publicUrlOrPath = getPublicUrlOrPath(process.env.PUBLIC_URL);

let publicUrlOrPath = isDev
  ? '/'
  : getPublicUrlOrPath(isDev, undefined, process.env.PUBLIC_URL);

if (isAdminCP && !isDev) {
  publicUrlOrPath = `${publicUrlOrPath}admincp/`;
}

if (isInstall && !isDev) {
  // publicUrlOrPath = `${publicUrlOrPath}install/`;
}

const moduleFileExtensions = [
  'web.mjs',
  'mjs',
  'web.js',
  'js',
  'web.ts',
  'ts',
  'web.tsx',
  'tsx',
  'json',
  'web.jsx',
  'jsx'
];

// Resolve file paths in the same order as webpack
const resolveModule = (resolveFn, filePath) => {
  const extension = moduleFileExtensions.find(extension =>
    fs.existsSync(resolveFn(`${filePath}.${extension}`))
  );

  if (extension) {
    return resolveFn(`${filePath}.${extension}`);
  }

  return resolveFn(`${filePath}.js`);
};

const wsPath = path.resolve(workbox.getRootDir());

const appHtml = resolveApp(
  process.env.MFOX_APP_HTML ?? 'public/index.ejs.html'
);

const appPublic = resolveApp(process.env.MFOX_APP_PUBLIC ?? 'public');

function getAppIndexJs() {
  if (isAdminCP) {
    return resolveModule(resolveApp, 'src/admincp');
  }

  if (isInstall) {
    return resolveModule(resolveApp, 'src/install');
  }

  return resolveModule(resolveApp, 'src/index');
}

const appIndexJs = getAppIndexJs();

const appBuild = resolveApp(`dist${publicUrlOrPath}`);

const proxyFile = process.env.MFOX_PROXY_FILE ?? 'proxy.json';
const proxyJson = resolveApp(proxyFile);

const appSettingJson = resolveApp(
  process.env.MFOX_SETTING_JSON ?? 'settings.json'
);

// config after eject: we're in ./config/
module.exports = {
  wsPath,
  publicPath: isInstall ? 'auto' : publicUrlOrPath,
  wsNodeModules: path.resolve(wsPath, 'node_modules'),
  wsPackages: path.resolve(wsPath, 'packages'),
  dotenv: resolveApp(process.env.MFOX_ENV_FILE ?? '.env'),
  appPath: resolveApp('.'),
  appBuild,
  appPublic,
  appHtml,
  appIndexJs,
  appPackageJson,
  appSettingJson,
  proxyJson,
  appSrc: resolveApp('src'),
  appTsConfig: resolveApp('tsconfig.json'),
  appJsConfig: resolveApp('jsconfig.json'),
  yarnLockFile: resolveApp('yarn.lock'),
  testsSetup: resolveModule(resolveApp, 'src/setupTests'),
  proxySetup: resolveApp('src/setupProxy.js'),
  appNodeModules: resolveApp('node_modules'),
  swSrc: resolveModule(resolveApp, 'src/service-worker'),
  publicUrlOrPath,
  moduleFileExtensions
};
