<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="container">
		<div id="in-ex-clusion-criteria">
			<div class="test">
				<div class="container-search-input">
					<div class="header-label">
						Einschlusskriterien
					</div>
					<div class="search-container">
						<div class="search-button">
							<button class="button-view" @click="switchSearchEinschlusskriterien">
								<svg role="img"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="folder"
									class="svg-inline--fa fa-folder fa-w-16 fa-2x"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor"
										d="M464 128H272l-64-64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V176c0-26.51-21.49-48-48-48z" />
								</svg>
							</button>
						</div>
						<div class="search-input">
							<NcTextField :value.sync="einschlussTextSerach"
								style="font-size: 16px;"
								label="Code oder Suchbegriff eingeben"
								trailing-button-icon="close"
								placeholder=" "
								:show-trailing-button="einschlussTextSerach !== ''"
								@trailing-button-click="einschlussTextSerach = ''"
								@input="searchCodeEinschlusskriterien">
								<Magnify :size="20" />
							</NcTextField>
						</div>
					</div>
				</div>
				<div class="pipe" />
				<div class="container-search-input">
					<div class="header-label">
						Ausschlusskriterien
					</div>
					<div class="search-container">
						<div class="search-button">
							<button class="button-view" @click="switchSearchAusschlusskriterien">
								<svg role="img"
									aria-hidden="true"
									focusable="false"
									data-prefix="fas"
									data-icon="folder"
									class="svg-inline--fa fa-folder fa-w-16 fa-2x"
									xmlns="http://www.w3.org/2000/svg"
									viewBox="0 0 512 512">
									<path fill="currentColor"
										d="M464 128H272l-64-64H48C21.49 64 0 85.49 0 112v288c0 26.51 21.49 48 48 48h416c26.51 0 48-21.49 48-48V176c0-26.51-21.49-48-48-48z" />
								</svg>
							</button>
						</div>
						<div class="search-input">
							<NcTextField :value.sync="ausschlussTextSerach"
								style="font-size: 16px;"
								label="Code oder Suchbegriff eingeben"
								trailing-button-icon="close"
								placeholder=" "
								:show-trailing-button="ausschlussTextSerach !== ''"
								@trailing-button-click="ausschlussTextSerach = ''"
								@input="searchCodeAusschlusskriterien">
								<Magnify :size="20" />
							</NcTextField>
						</div>
					</div>
				</div>
			</div>
			<SearchTreeOverlayContent v-if="isEinschlusskriterienOverlayOpen" :filtered-arr="filteredArr" />
			<SearchTreeOverlayContent v-if="isAusschlusskriterienOverlayOpen" :is-ausschlusskriterien="isAusschlusskriterien" :filtered-arr="filteredArr" />
		</div>

		<div id="feasibility-query__output">
			<div class="card-header">
				<div class="text-header">
					<label style="font-size: 16px; font-weight: 500;">Ausgewählte Merkmale</label>
				</div>
			</div>
			<div class="card-content">
				<div class="textfield" />
				<div class="pipe2" />
				<div class="textfield" />
			</div>
		</div>
	</div>
</template>

<script>
import NcTextField from '@nextcloud/vue/dist/Components/NcTextField.js'
import Magnify from 'vue-material-design-icons/Magnify.vue'
import SearchTreeOverlayContent from './SearchTreeOverlayContent.vue'

export default {
	name: 'FeasibilityQueryBuilder',
	components: {
		NcTextField,
		Magnify,
		SearchTreeOverlayContent,
	},
	data() {
		return {
			einschlussTextSerach: '',
			ausschlussTextSerach: '',
			isEinschlusskriterienOverlayOpen: false,
			isAusschlusskriterienOverlayOpen: false,
			isAusschlusskriterien: true,
			mockupArr: ['Test1', 'Test2', 'Test3'],
			filteredArr: [],
		}
	},
	computed: {},
	// life cycle of vue js
	// Call functions before all component are rendered
	beforeCreate() {},
	// Call functions before the template is rendered
	created() {},
	beforeMount() {},
	mounted() {},
	beforeUpdate() {},
	updated() {
		const buttonContainer = document.getElementsByClassName('input-field__main-wrapper')
		if (buttonContainer[1].querySelector('button') !== null) {
			buttonContainer[1].querySelector('button').addEventListener('click', this.searchCodeEinschlusskriterien)
		}
		if (buttonContainer[2].querySelector('button') !== null) {
			buttonContainer[2].querySelector('button').addEventListener('click', this.searchCodeAusschlusskriterien)
		}
	},
	beforeDestroy() {},
	destroyed() {},

	methods: {
		switchSearchEinschlusskriterien() {
			this.isEinschlusskriterienOverlayOpen = !this.isEinschlusskriterienOverlayOpen
			this.isAusschlusskriterienOverlayOpen = false
			this.fillteredSearch(this.einschlussTextSerach)
		},
		switchSearchAusschlusskriterien() {
			this.isAusschlusskriterienOverlayOpen = !this.isAusschlusskriterienOverlayOpen
			this.isEinschlusskriterienOverlayOpen = false
			this.fillteredSearch(this.ausschlussTextSerach)
		},
		searchCodeEinschlusskriterien() {
			this.isEinschlusskriterienOverlayOpen = true
			this.isAusschlusskriterienOverlayOpen = false
			this.fillteredSearch(this.einschlussTextSerach)
		},
		searchCodeAusschlusskriterien() {
			this.isAusschlusskriterienOverlayOpen = true
			this.isEinschlusskriterienOverlayOpen = false
			this.fillteredSearch(this.ausschlussTextSerach)
		},
		fillteredSearch(textSearch) {
			this.filteredArr = this.mockupArr.filter((item) =>
				item.toLowerCase().includes(textSearch.toLowerCase()),
			)
		},
	},
}
</script>
<style scoped>
.container {
    display: flex;
    flex-direction: column;
    flex-wrap: wrap;
    width: 100%;
    row-gap: 20px;
}

#in-ex-clusion-criteria {
	flex-direction: column;
	box-sizing: border-box;
	display: flex;
	place-content: stretch space-between;
	align-items: stretch;
}

.test {
    display: flex;
    flex-direction: row;
    column-gap: 20px;
    width: 100%;
}

.container-search-input {
	flex: 1 1 100%;
	box-sizing: border-box;
	max-width: 48.5%;
	position: relative;
	min-height: 6em;
	border: 1px solid #9ea9b3;
	border-radius: 5px;
}

.header-label {
	min-width: min-content;
	background-color: #5270a7;
	color: #ffffff;
	text-align: center;
	font-size: 16px;
	font-weight: 500;
	padding: 2px 0;
}

.search-container {
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	justify-content: space-around;
    align-items: baseline;
	margin: 10px;
}

.search-button {
	place-content: stretch center;
	align-items: stretch;
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
}

.button-view {
	border-radius: 4px;
	border-width: 1px;
	border-style: solid;
	height: 44px;
	width: 44px;
	line-height: unset;
	color: white;
	background-color: #5e6c78;
}

.button-view:hover {
	background-color: #9ea9b3;
}

.button-view:active {
	background-color: #5e6c78 !important;
	color: white !important;
}

.search-input {
	flex: 1 1 100%;
	box-sizing: border-box;
	max-width: 85%;
	font-size: 14px;
}

.pipe {
	flex: 1 1 100%;
	box-sizing: border-box;
	max-width: 1%;
	background-color: #5270a7;
	border-radius: 4px;
}

.pipe2 {
	flex: 1 1 100%;
	box-sizing: border-box;
	max-width: 1%;
	background-color: #5270a7;
}

#feasibility-query__output {
	border: 1px solid #9ea9b3;
	border-top-right-radius: 5px;
	border-top-left-radius: 5px;
	box-shadow: none!important;
	width: 100%;
}

.card-header {
	display: flex;
	padding-left: 10px;
	background: #5270a7;
	color: #ffffff;
	border-top-right-radius: 4px;
	border-top-left-radius: 4px;
	min-height: 3.5em;
}

.text-header {
	flex-direction: column;
	box-sizing: border-box;
	display: flex;
	place-content: stretch space-around;
	align-items: stretch;
}

.card-content {
	display: flex;
	flex-direction: row;
	min-height: 150px;
}

.card-content2 {
	padding-bottom: 16px;
	display: block;
}

.textfield2 {
	flex-direction: row;
	box-sizing: border-box;
	display: flex;
	place-content: stretch space-between;
	align-items: stretch;
	height: 100%;
	max-width: 100%;
	flex: 1 1 100%;
}

.textfield {
	flex: 1 1 100%;
	border: dashed 1px black;
	margin: 20px;
	padding: 10px;
}
</style>
