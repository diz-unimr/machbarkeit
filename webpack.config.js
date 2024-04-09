// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later
const webpackConfig = require("@nextcloud/webpack-vue-config");

// use source-map for dev env, too
webpackConfig.devtool = "source-map";

module.exports = webpackConfig;
