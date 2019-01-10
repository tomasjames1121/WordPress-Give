/* globals process, __dirname, module  */

/**
 * External dependencies
 */
const path                    = require('path');
const webpack                 = require('webpack');
const CopyWebpackPlugin       = require('copy-webpack-plugin');
const MiniCSSExtractPlugin    = require('mini-css-extract-plugin');
const BrowserSyncPlugin       = require('browser-sync-webpack-plugin');
const ImageminPlugin          = require('imagemin-webpack-plugin').default;
const CleanWebpackPlugin      = require('clean-webpack-plugin');
const WebpackRTLPlugin        = require('webpack-rtl-plugin');
const wpPot                   = require('wp-pot');
const UglifyJsPlugin          = require('uglifyjs-webpack-plugin');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");

const inProduction = ('production' === process.env.NODE_ENV);
const mode         = inProduction ? 'production' : 'development';

const config = {
	mode,

	entry: {
		give: ['./assets/src/css/frontend/give-frontend.scss', './assets/src/js/frontend/give.js'],
		admin: ['./assets/src/css/admin/give-admin.scss', './assets/src/js/admin/admin.js'],
		'babel-polyfill': '@babel/polyfill',
		gutenberg: './blocks/load.js',
		'admin-shortcode-button': ['./assets/src/css/admin/shortcodes.scss'],
		'admin-shortcodes': './includes/admin/shortcodes/admin-shortcodes.js',
		'plugin-deactivation-survey': ['./assets/src/css/admin/plugin-deactivation-survey.scss', './assets/src/js/admin/plugin-deactivation-survey.js'],
	},
	output: {
		path: path.join(__dirname, './assets/dist/'),
		filename: 'js/[name].js',
	},

	// Ensure modules like magnific know jQuery is external (loaded via WP).
	externals: {
		$: 'jQuery',
		jquery: 'jQuery',
		lodash: 'lodash',
	},
	devtool: 'source-map',
	module: {
		rules: [

			// Use Babel to compile JS.
			{
				test: /\.js$/,
				exclude: /node_modules/,
				loader: 'babel-loader',
			},

			// Expose accounting.js for plugin usage.
			{
				test: require.resolve('accounting'),
				use: [
					{
						loader: 'expose-loader',
						options: 'accounting',
					}
				],
			},

			// Create RTL styles.
			{
				test: /\.css$/,
				use: [
					MiniCSSExtractPlugin.loader,
					{
						loader: 'style-loader',
						options: {
							sourceMap: ! inProduction,
						},
					}
				],
			},

			// SASS to CSS.
			{
				test: /\.scss$/,
				use: [
					MiniCSSExtractPlugin.loader,
					{
						loader: 'css-loader',
						options: {
							sourceMap: ! inProduction,
						},
					},
					{
						loader: 'sass-loader',
						options: {
							sourceMap: ! inProduction,
							outputStyle: (inProduction ? 'compressed' : 'nested'),
						},
					}],
			},

			// Font files.
			{
				test: /\.(ttf|otf|eot|woff(2)?)(\?[a-z0-9]+)?$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: 'fonts/[name].[ext]',
							publicPath: '../',
						},
					},
				],
			},

			// Image files.
			{
				test: /\.(png|jpe?g|gif|svg)$/,
				use: [
					{
						loader: 'file-loader',
						options: {
							name: 'images/[name].[ext]',
							publicPath: '../',
						},
					},
				],
			},
		],
	},

	// Plugins. Gotta have em'.
	plugins: [

		// Removes the "dist" folder before building.
		new CleanWebpackPlugin(['assets/dist']),

		new MiniCSSExtractPlugin({
			filename: "css/[name].css"
		}),

		// Create RTL css.
		new WebpackRTLPlugin({
			suffix: '-rtl',
			minify: inProduction,
		}),

		// Copy images and SVGs
		new CopyWebpackPlugin([{from: 'assets/src/images', to: 'images'}]),

		// Minify images.
		// Must go after CopyWebpackPlugin above: https://github.com/Klathmon/imagemin-webpack-plugin#example-usage
		new ImageminPlugin({test: /\.(jpe?g|png|gif|svg)$/i}),

		// Setup browser sync. Note: don't use ".local" TLD as it will be very slow. We recommending using ".test".
		new BrowserSyncPlugin({
			files: [
				'**/*.php',
			],
			host: 'localhost',
			port: 3000,
			proxy: 'give.test',
		}),
	],

	optimization: {
		minimizer: [
			new UglifyJsPlugin({
				uglifyOptions: {
					output: {
						comments: false
					},
				},
				sourceMap: ! inProduction
			}),
			new OptimizeCSSAssetsPlugin({
				cssProcessor: require('cssnano'),
				cssProcessorPluginOptions: {
					preset: ['default', { discardComments: { removeAll: true } }],
				}
			}),
		]
	}
};

if (inProduction) {
	// POT file.
	wpPot({
		package: 'Give',
		domain: 'give',
		destFile: 'languages/give.pot',
		relativeTo: './',
		bugReport: 'https://github.com/impress-org/give/issues/new',
		team: 'GiveWP <info@givewp.com>',
	});
}

module.exports = config;

