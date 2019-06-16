const path                    = require('path');
const webpack                 = require('webpack');

const config = {
	entry: {
		app: './assets/src/js/app.js',
	},
	output: {
		path: path.resolve(__dirname, 'public'),
		filename: './js/bundle.js',
		publicPath: '/public/'
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /(node_modules)/,
				loader: 'babel-loader'
			},
			{
                test:/\.css$/,
                use:['style-loader','css-loader']
            }
		]
	},
};

module.exports = config;