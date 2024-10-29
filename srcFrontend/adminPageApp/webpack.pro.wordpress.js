const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const TerserJSPlugin = require('terser-webpack-plugin');
const OptimizeCssAssetsPlugin = require('optimize-css-assets-webpack-plugin');

const mode = "development";
const plugins = [
    new MiniCssExtractPlugin({
        filename: 'bundle.css',
    }),
];


module.exports = {
    mode,
    plugins,
    entry: './src/indexPro.js',
    output: {
        filename: 'build-app.js',
        path: path.resolve(__dirname, '../../src/assets/admin-app'),
        clean: true,
    },

    resolve: {
        alias: {
            'ApiClientLib': path.resolve(__dirname, '../apiClientLib/src'),
            'ApiClient': path.resolve(__dirname, 'src/apiClient.js'),
            'AppConfig': path.resolve(__dirname, 'src/appConfig.js'),
            'Components': path.resolve(__dirname, 'src/components'),
            'BaseComponents': path.resolve(__dirname, 'src/base'),
            'ProComponents': path.resolve(__dirname, 'src/pro'),
            'Store': path.resolve(__dirname, 'src/store'),
        },
        extensions: ['.ts', '.js', '.json']
    },

    module: {
        rules: [
            {
                test: /\.jsx?$/,
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                    options: {
                        cacheDirectory: true,
                    },
                },
            },
            {
                test: /\.(ts)?$/,
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
            {
                test: /\.(s[ac]|c)ss$/i,
                use: [
                    MiniCssExtractPlugin.loader,
                    'css-loader',
                ],
            },
            {
                test: /\.(png|jpe?g|gif|svg|webp|ico)$/i,
                type: 'assets/resource',
            },
            {
                test: /\.(woff2?|eot|ttf|otf)$/i,
                type: 'assets/resource',
            },
        ],
    },
    optimization: {
        minimizer: [
            new TerserJSPlugin({
                terserOptions: {
                    compress: {
                        // drop_console: true,
                    },
                    output: {
                        comments: false,
                    }
                },
                extractComments: false,
            }),
            new OptimizeCssAssetsPlugin({})
        ],
    },
};
