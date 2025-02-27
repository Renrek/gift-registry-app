const path = require('path');
const MiniCssExtractPlugin = require("mini-css-extract-plugin");
const TerserPlugin = require("terser-webpack-plugin");

module.exports = (env) => {

    const isProduction = (env.production !== undefined);
    const isDevelopment = (env.development !== undefined);

    
    
    const plugins = isProduction ? [
        new MiniCssExtractPlugin({filename: "[name].[contenthash].css"})
    ] : [];

    const optimization = isProduction ? { 
        minimizer: [new TerserPlugin()],
        splitChunks: {
            chunks: 'async',
            minSize: 20000,
            minRemainingSize: 0,
            minChunks: 1,
            maxAsyncRequests: 30,
            maxInitialRequests: 30,
            enforceSizeThreshold: 50000,
            cacheGroups: {
                defaultVendors: {
                    test: /[\\/]node_modules[\\/]/,
                    priority: -10,
                    reuseExistingChunk: true,
                },
                default: {
                    minChunks: 2,
                    priority: -20,
                    reuseExistingChunk: true,
                },
            },
        },
    } : {};
    
    return {
        mode: isProduction ? "production" : "development",
        entry: {
            main: "./components/main.entry.tsx",
            utils: "./components/utils.entry.ts"
        },
        output: {
            filename: isProduction ? "[name].[contenthash].js" : "[name].js",
            path: path.resolve(__dirname, "public/assets"),
            clean: true,
        },
        //optimization: isProduction ? { minimizer: [new OptimizeCssAssetsPlugin]} // optimize-css-assets-webpack-plugin
        optimization: optimization,
        plugins: plugins,
        module: {
            rules: [
                {
                    test: /\.js$/,
                    exclude: [/node_modules/, /vendor/],
                    use : {
                        loader: 'babel-loader',
                        options: {
                            presets: [
                                ['@babel/preset-env', { targets: "defaults" }]
                            ]
                        }
                    }
                },
                {
                    test: /\.(tsx|ts)$/,
                    use: 'ts-loader',
                    exclude: [/node_modules/, /vendor/],
                },
                {
                    test: /\.scss$/,
                    use: [
                        isProduction ? MiniCssExtractPlugin.loader : "style-loader", 
                        "css-loader", 
                        {
                            loader: "sass-loader",
                            options: {
                                implementation: require("sass"),
                                sassOptions: {
                                    fiber: false,
                                },
                            },
                        },
                    ]
                },
                {
                    test: /\.(svg|png|jpg|gif)$/,
                    exclude: [/node_modules/, /vendor/],
                    use: {
                        loader: "file-loader",
                        options: {
                            name: "[name].[hash].[ext]",
                            outputPath: "img"
                        }
                    }
                }
            ]
        },
        resolve: {
            extensions: ['.tsx', '.ts', '.js', '.scss'],
        },
        devServer:{
            hot: true,
            proxy: {
                '*':'http://localhost:80'
            }
        }
    }
}