const includeEnv = {};

const isMultiTarget = process.env.MFOX_BUILD_PROFILE === 'multi-target';

// do not override in multi target
const skips = ['MFOX_BUILD_TYPE', 'MFOX_BUILD_TYPE', 'MFOX_BUILD'];

/**
 * PUT DEFAULT CONST TO PREVENT missing `process` from webpack@v5+
 *
 * @type {{MFOX_LOADING_BG: string, PUBLIC_URL: string, NODE_ENV: string, MFOX_BUILD_TYPE: string}}
 */
const defaults = {
  NODE_ENV: 'development',
  ASSET_PATH: '',
  PUBLIC_URL: '',
  MFOX_BUILD_TYPE: 'web',
  MFOX_LOADING_BG: '#2d2d2d',
  MFOX_SITE_NAME: 'MetaFox',
  MFOX_SITE_DESCRIPTION: '',
  MFOX_SITE_KEYWORDS: '',
  MFOX_SITE_TITLE: '',
  MFOX_END_HEAD_HTML: '',
  MFOX_END_BODY_HTML: '',
  MFOX_START_BODY_HTML: '',
  MFOX_FAVICON_URL: '/favicon.ico',
  MFOX_MASK_ICON_URL: '/safari-pinned-tab.svg',
  MFOX_APPLE_TOUCH_ICON_URL: '/apple-touch-icon.png',
  MFOX_LOCALE: 'en',
  MFOX_COOKIE_PREFIX: 'yA0JuFD6n6zkC1',
  MFOX_BUILD_AT: new Date().toUTCString() // help for debug frontend on client site.
};

if (!process.env.ASSET_PATH) {
  process.env.ASSET_PATH = process.env.PUBLIC_URL;
}

if (isMultiTarget) {
  // override process.env
  Object.keys(process.env)
    .filter(name => /^MFOX_/.test(name))
    .filter(name => !skips.includes(name))
    .forEach(name => (process.env[name] = `___${name}___`));
}

Object.keys(process.env)
  .filter(name => /MFOX_/.test(name))
  .forEach(name => (includeEnv[name] = process.env[name]));

const exampleEnv = Object.assign({}, defaults, includeEnv, {
  NODE_ENV: process.env.NODE_ENV || 'development',
  ASSET_PATH: process.env.ASSET_PATH,
  PUBLIC_URL: process.env.PUBLIC_URL || '',
  MFOX_BUILD_TYPE: process.env.MFOX_BUILD_TYPE,
  MFOX_BUILD: true,
  MFOX_BUILT_AT: new Date().toISOString()
});

module.exports = exampleEnv;
