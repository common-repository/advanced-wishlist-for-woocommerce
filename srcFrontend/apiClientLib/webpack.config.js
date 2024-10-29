const path = require('path');
const TerserJSPlugin = require('terser-webpack-plugin');

const mode = 'production';

module.exports = {
  mode,
  entry: './index.ts',
  module: {
    rules: [
      {
        test: /\.(ts|js)?$/,
        exclude: [
          /node_modules/,
          /dist/
        ],
        use: {
          loader: "babel-loader",
          options: {
            presets: ["@babel/preset-env", "@babel/preset-typescript"],
          },
        },
      },
    ],
  },
  resolve: {
    extensions: [".ts", ".js"],
  },
  output: {
    filename: 'api-client-lib-build-app.js',
    path: path.resolve(__dirname, '../../src/assets/api-client-lib'),
    clean: true,
  },
    optimization: {
        minimizer: [
            new TerserJSPlugin({
                terserOptions: {
                    compress: {
                        drop_console: true,
                    },
                    output: {
                        comments: false,
                    }
                },
                extractComments: false,
            })
        ],
    },
};
