// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later
const webpackConfig = require('@nextcloud/webpack-vue-config')
const Dotenv = require('dotenv-webpack')

// use source-map for dev env, too
webpackConfig.devtool = 'source-map'
webpackConfig.devServer.port = 9000

let envPath
switch (process.env.NODE_ENV) {
    case 'development':
        envPath = './.development.env'
        break
    case 'production':
        envPath = './.production.env'
        break
}

if (envPath) {
    webpackConfig.plugins.push(
        new Dotenv({
            path: envPath,
            safe: true
        }))
}

module.exports = webpackConfig
