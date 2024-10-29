const path = require('path');
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const TerserJSPlugin = require("terser-webpack-plugin");
const OptimizeCssAssetsPlugin = require("optimize-css-assets-webpack-plugin");

const mode = "development";
const plugins = [
    new MiniCssExtractPlugin({
        filename: '[name].[contenthash].css',
    }),
    new HtmlWebpackPlugin({
        template: './public/index.html',
    }),
];


module.exports = {
    mode,
    plugins,
    entry: './src/indexPro.js',
    output: {
        filename: '[name].[contenthash].js',
        path: path.resolve(__dirname, 'dist'),
        assetModuleFilename: 'assets/[hash][ext][query]',
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

    devtool: 'source-map',
    devServer: {
        static: {
            directory: path.join(__dirname, 'dist'),
        },
        compress: true,
    },

    module: {
        rules: [
            {test: /\.(html)$/, use: ['html-loader']},
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
                type: 'asset/resource',
            },
            {
                test: /\.(woff2?|eot|ttf|otf)$/i,
                type: 'asset/resource',
            },
        ],
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
            }),
            new OptimizeCssAssetsPlugin({})
        ],
    },
};
