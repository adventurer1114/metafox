const fs = require('fs');
const path = require('path');

const NODE_ENV = process.env.NODE_ENV || 'development';
const APP_PATH = process.env.APP_PATH || path.resolve(process.cwd(), 'app');

function findWorkboxRoot() {
  let workboxRoot = process.cwd();
  for (let i = 0; 4 > i; ++i) {
    if (fs.existsSync(path.resolve(workboxRoot, '.workboxrc'))) {
      return workboxRoot;
    } else {
      workboxRoot = path.resolve(workboxRoot, '../');
    }
  }
}

if (!process.env.WORKBOX_ROOT) {
  process.env.WORKBOX_ROOT = findWorkboxRoot();
}

const profile = process.env.MFOX_BUILD_PROFILE || 'metafox';
const dotEnvFile = path.resolve(APP_PATH, '.env');

const envFile = [
  `${dotEnvFile}.${NODE_ENV}.${profile}.local`,
  `${dotEnvFile}.${NODE_ENV}.local`,
  `${dotEnvFile}.${NODE_ENV}.${profile}`,
  `${dotEnvFile}.${profile}`,
  `${dotEnvFile}.${NODE_ENV}`,
  `${dotEnvFile}`
]
  .filter(Boolean)
  .find(fs.existsSync);

if (envFile) {
  const basename = path.basename(envFile);
  require('dotenv').config({
    path: envFile
  });
  process.env.MFOX_ENV_FILE = basename;
}
