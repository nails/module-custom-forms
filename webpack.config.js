const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const path = require('path');


module.exports = {
    entry: {
        'admin': './assets/js/admin.js'
    },
    output: {
        filename: '[name].min.js',
        path: path.resolve(__dirname, 'assets/js/')
    },
    module: {
        rules: [
            {
                test: /\.(css|scss|sass)$/,
                use: [
                    MiniCssExtractPlugin.loader,
                    {
                        loader: 'css-loader',
                        options: {
                            url: false
                        }
                    },
                    'postcss-loader',
                    'sass-loader'
                ]
            },
        ]
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '../css/[name].min.css',
            allChunks: true
        }),
    ],
    mode: 'production'
};
