const axios = require('axios');
const fs = require('fs');
const { get } = require('lodash');
const { execSync } = require('child_process');
const os = require('os');
const path = require('path');
const fsExtra = require('fs-extra');
const appSetting = require('@metafox/react-app/settings.json');

// package does need to download
const excludeDownloadPackages = {
  '@framework/kernel': 'local',
  '@metafox/core': 'local',
  '@metafox/form': 'local',
  '@metafox/storage': 'local',
  '@framework/yup': 'local',
  '@framework/localize': 'local',
  '@framework/form': 'local',
  '@metafox/app': '5.0.1',
  '@metafox/captcha': '5.0.1',
  '@metafox/static-page': '5.0.1',
  '@metafox/flood': '5.0.1',
  '@framework/layout': '5.0.1',
  '@metafox/menu': '5.0.1',
  '@metafox/schedule': '5.0.1',
  '@metafox/core': '5.0.1',
  '@metafox/emoji': '5.0.1',
  '@metafox/user': '5.0.1',
  '@metafox/notification': '5.0.1',
  '@metafox/report': '5.0.1',
  '@metafox/activity-point': '5.0.1',
  '@metafox/feed': '5.0.1',
  '@metafox/share': '5.0.1',
  '@metafox/announcement': '5.0.1',
  '@metafox/bgstatus': '5.0.1',
  '@metafox/blog': '5.0.1',
  '@metafox/chat': '5.0.1',
  '@metafox/chatplus': '5.0.1',
  '@metafox/comment': '5.0.1',
  '@metafox/event': '5.0.1',
  '@metafox/forum': '5.0.1',
  '@metafox/friend': '5.0.1',
  '@metafox/group': '5.0.1',
  '@metafox/like': '5.0.1',
  '@metafox/reaction': '5.0.1',
  '@metafox/marketplace': '5.0.1',
  '@metafox/music': '5.0.1',
  '@metafox/page': '5.0.1',
  '@metafox/paypal': '5.0.1',
  '@metafox/photo': '5.0.1',
  '@metafox/poll': '5.0.1',
  '@metafox/quiz': '5.0.1',
  '@metafox/rad': '5.0.1',
  '@metafox/saved': '5.0.1',
  '@metafox/search': '5.0.1',
  '@metafox/sticker': '5.0.1',
  '@metafox/subscription': '5.0.1',
  '@metafox/video': '5.0.1'
};

function generateRandom() {
  return (
    Math.random().toString(36).substring(2, 15) +
    Math.random().toString(23).substring(2, 5)
  );
}

function getDependencyDestination(registryDir, packageName, appVersion) {
  const name = `${packageName}-${appVersion}`.replace(/([^\w_\.]+)/g, '-');

  return path.join(registryDir, `${name}.zip`);
}

function getFileSize(locationPath) {
  let size = fs.statSync(locationPath).size / 1048576;

  size = Math.round(size * 100) / 100;

  return `${size} MB`;
}

async function downloadDependency(fileUrl, destination) {
  if (fs.existsSync(destination)) {
    return;
  }

  const writer = fs.createWriteStream(destination);

  return axios({
    method: 'get',
    url: fileUrl,
    responseType: 'stream'
  }).then(response => {
    return new Promise((resolve, reject) => {
      response.data.pipe(writer);
      let error = null;
      writer.on('error', err => {
        error = err;
        writer.close();
        reject(err);
      });
      writer.on('close', () => {
        if (!error) {
          resolve(true);
        }
      });
    });
  });
}

async function ensureAppDependency(
  platformVersion,
  packageName,
  appVersion,
  destination
) {
  console.log(`try to download ${packageName}:${appVersion}`);

  const json = await axios.post(
    'https://api.phpfox.com',
    {
      id: packageName,
      version: platformVersion,
      app_version: appVersion,
      version_type: 'frontend'
    },
    {
      auth: {
        username: process.env.MFOX_LICENSE_ID,
        password: process.env.MFOX_LICENSE_KEY
      },
      'X-Product': 'metafox',
      'X-Namespace': 'phpfox',
      'X-API-Version': '1.1'
    }
  );

  const url = get(json, 'data.data.download');

  if (json.data.status !== 'success' || !url) {
    throw new Error(JSON.stringify(json.data));
  }

  const tempFile = path.join(os.tmpdir(), `${generateRandom()}.zip`);
  await downloadDependency(url, tempFile);

  console.log(`downloaded ${tempFile} ${getFileSize(tempFile)}`);

  const tmpDir = path.join(os.tmpdir(), fs.mkdtempSync('download'));

  execSync(`unzip ${tempFile} -d ${tmpDir}`);

  // build frontend path.

  const tmpFrontendDir = `${tmpDir}/frontend`;
  const tmpFrontendFile = `${tmpFrontendDir}/bundled_frontend_file.zip`;

  if (fs.existsSync(tmpFrontendDir)) {
    execSync(`(cd ${tmpFrontendDir} && zip -r ${tmpFrontendFile} ./)`);
  }

  if (fs.existsSync(tmpFrontendFile)) {
    fs.copyFileSync(tmpFrontendFile, destination);
  }

  // clean up temporary dir.
  execSync(`rm -rf ${tmpDir}`);
  execSync(`rm -rf ${tempFile}`);
}

const ensureDownload = async (
  platformVersion,
  packageName,
  appVersion,
  destination
) => {
  return ensureAppDependency(
    platformVersion,
    packageName,
    appVersion,
    destination
  )
    .then(() => {
      console.log(`downloaded ${packageName}:${appVersion} to ${destination}`);
    })
    .catch(err => console.error(err.message));
};

async function downloadAppDependencies(extractTo, registryDir) {
  const platformVersion = '5.0.0';

  const { packages } = appSetting;

  // try to download file lists
  const packageNames = Object.keys(packages);

  for (const packageName of packageNames) {
    if (excludeDownloadPackages[packageName]) {
      continue;
    }

    const appVersion = packages[packageName];
    const destination = getDependencyDestination(
      registryDir,
      packageName,
      appVersion
    );

    if (!fs.existsSync(destination)) {
      await ensureDownload(
        platformVersion,
        packageName,
        appVersion,
        destination
      );
    }

    // extract downloaded file to temporary files.
    if (fs.existsSync(destination)) {
      execSync(`unzip ${destination} -d ${extractTo}`);
    }
  }
}

async function ensureAppDependencies() {
  const inputDir = process.env.MFOX_INPUT_DIR;
  const registryDir = process.env.MFOX_REGISTRY_DIR;

  if (
    !inputDir ||
    !registryDir ||
    !process.env.MFOX_LICENSE_ID ||
    !process.env.MFOX_LICENSE_KEY
  ) {
    return;
  }

  if (!fs.existsSync(inputDir)) {
    fs.mkdirSync(inputDir, { recursive: true });
  }

  downloadAppDependencies(inputDir, registryDir).catch(err =>
    console.error(err.message)
  );

  // copy overwrite files.
  if (fs.existsSync(inputDir)) {
    fsExtra.copySync(inputDir, process.cwd(), { overwrite: true });
  }
}

module.exports = ensureAppDependencies;
