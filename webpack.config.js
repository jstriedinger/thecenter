const path = require('path');
const webpack = require('webpack');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const CopyWebpackPlugin = require('copy-webpack-plugin');
const BrowserSyncPlugin = require('browser-sync-webpack-plugin');



const config = {
  context: path.resolve(__dirname, "src"),
  // configurations here
  entry: {
    app: './js/app.js'
  },
  output: {
    filename: './js/[name].js',
    // Output path using nodeJs path module
    path: path.resolve(__dirname, 'dist')
  },
  // Adding jQuery as external library
  externals: {
    jquery: 'jQuery'
  },
  module: {
    rules: [
      {
        test: /\.scss$/,
        use: [
          MiniCssExtractPlugin.loader,
          { loader: 'css-loader', options: { url: false, sourceMap: true } }, 
          { loader: 'postcss-loader', options: { sourceMap: true } },
          { loader: 'sass-loader', options: { sourceMap: true } }
        ]
      },
      {
        test: /\.js$/,
        exclude: /(node_modules|bower_components)/,
        use: [
          {
              loader: 'babel-loader',
              options: {
                  presets: ['env']
              }   
          }
        ]
      }
    ]
  },
  watch: true,
  plugins: [
    
    require('autoprefixer'),
    new MiniCssExtractPlugin({filename: "/css/[name].css"}),
    new BrowserSyncPlugin({
        files: [
          './**/*.php',
          './**/*.twig'
        ],
        host: 'localhost',
        port: 3000,
        proxy: 'http://localhost/center',
        logPrefix: 'webpack',
        logLevel: 'debug',
        ghostMode: false
    })
  ]
};

module.exports = config;