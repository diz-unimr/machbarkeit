/**
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
**/

import { generateFilePath } from '@nextcloud/router'
import Vuex from 'vuex'
import store from './store.js' // Import the Vuex store
import Vue from 'vue'
import App from './App.vue'

Vue.use(Vuex)
// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, '', 'js/')

Vue.mixin({ methods: { t, n } })

export default new Vue({
	el: '#content',
	store,
	render: h => h(App),
})
