const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');
const glob = require('glob');

const entries = {};

const files = glob.sync('./blocks/**/editor.js');

files.forEach((file) => {
  const blockDir = path.dirname(file);
  const relativeDir = path.relative('./blocks', blockDir).replace(/\\/g, '/');
  const entryName = path.posix.join('blocks', relativeDir, 'editor');
  entries[entryName] = path.resolve(process.cwd(), file);
});

module.exports = {
  ...defaultConfig,
  entry: entries,
  // Add source maps for better debugging
  devtool: process.env.NODE_ENV === 'production' ? 'source-map' : 'eval-source-map',
};
