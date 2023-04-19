/**
 * handle save layout configuration on frontend dev server
 */
const fs = require('fs');
const os = require('os');
const { isEqual } = require('lodash');

function writeJsonFile(filename, data) {
  const newContent = JSON.stringify(data, null, '  ') + os.EOL;

  if (fs.existsSync(filename)) {
    const oldContent = fs.readFileSync(filename, { encoding: 'utf-8' });
    if (oldContent === newContent) {
      return;
    }
  }

  fs.writeFileSync(filename, newContent, {
    encoding: 'utf-8'
  });

  console.log('Updated ' + filename);
}

function updateThemeVariantConfig(filename, data) {
  writeJsonFile(filename, data);
}

function updateDefaultTemplate(pageName, data) {
  const map = JSON.parse(
    fs.readFileSync('scripts/templates.map.json', { encoding: 'utf-8' })
  );

  let filename = map[pageName];

  if (!filename && data.pageNameAlt) {
    filename = map[data.pageNameAlt];
  }

  if (!filename) return;

  const oldJson = JSON.parse(fs.readFileSync(filename, { encoding: 'utf-8' }));

  // no change
  if (isEqual(oldJson[pageName], data)) return;

  console.log(`Updated ${filename}`);

  oldJson[pageName] = data;

  writeJsonFile(filename, oldJson);
}

function updateDefaultPage(pageName, data) {
  const map = JSON.parse(
    fs.readFileSync('scripts/pages.map.json', { encoding: 'utf-8' })
  );

  let filename = map[pageName];

  if (!filename && data.pageNameAlt) {
    filename = map[data.pageNameAlt];
  }

  if (!filename) return;

  const oldJson = JSON.parse(fs.readFileSync(filename, { encoding: 'utf-8' }));

  // no change
  if (isEqual(oldJson[pageName], data)) return;

  console.log(`Updated ${filename}`);

  oldJson[pageName] = data;

  writeJsonFile(filename, oldJson);
}

function saveLayout(payload, req, res) {
  const { files, theme: themeId } = payload;

  files.forEach(obj => {
    const { filename, name, content } = obj;

    writeJsonFile(filename, content);

    if (themeId == 'a0' || themeId == 'admincp') {
      if (name === 'pageLayouts')
        Object.keys(content).forEach(name =>
          updateDefaultPage(name, content[name])
        );
      if (name === 'templates')
        Object.keys(content).forEach(name =>
          updateDefaultTemplate(name, content[name])
        );
      if (name === 'variant')
        Object.keys(content).forEach(name =>
          updateThemeVariantConfig(filename, content)
        );
    }
  });

  res.writeHead(200, 'OK', { 'Content-Type': 'application/json' });
  res.write(JSON.stringify({ status: 'success', message: 'Save changes' }));
  res.end();
}

function saveVariant(payload, req, res) {
  const { files, theme: themeId } = payload;

  files.forEach(obj => {
    const { filename, name, content } = obj;

    writeJsonFile(filename, content);

    if (themeId == 'a0' || themeId == 'admincp') {
      if (name === 'variant')
        Object.keys(content).forEach(name =>
          updateThemeVariantConfig(filename, content)
        );
    }
  });

  res.writeHead(200, 'OK', { 'Content-Type': 'application/json' });
  res.write(JSON.stringify({ status: 'success', message: 'Save changes' }));
  res.end();
}

module.exports = {
  saveLayout,
  saveVariant
};
