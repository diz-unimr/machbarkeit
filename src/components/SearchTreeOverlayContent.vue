<template>
	<!--
		SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
		SPDX-License-Identifier: AGPL-3.0-or-later
	-->
	<div class="test">
		<div class="search-tree-overlay-container" :class="{ 'ausschlusskriterien-overlay': isAusschlusskriterien }">
			<div class="search-tree-head">
				<div class="search-tree-button">
					<div>Alle</div>
					<div>Diagnose</div>
					<div>Prozedur</div>
					<div>Person</div>
					<div>Laboruntersuchung</div>
					<div>Medikamentenverabreichung</div>
					<div>Bioprobe</div>
					<div>Einwilligung</div>
				</div>
			</div>
			<div class="ontology-tree">
				{{ filteredArr }}
			</div>
		</div>
	</div>
</template>

<script>
export default {
	name: 'SearchTreeOverlayContent',
	props: {
		isAusschlusskriterien: {
			type: Boolean,
			default: false,
		},
		filteredArr: {
			type: Array,
			default: Array,
		},
	},
}

</script>
<style scoped>
.search-tree-overlay-container {
    display: flex;
    flex-direction: column;
    position: absolute;
    box-sizing: border-box;
    min-height: 12em;
    background-color: #fff;
    box-shadow: 0 11px 15px -4px #0003, 0 24px 38px 5px #00000024, 0 9px 46px 8px #0000001f;
    pointer-events: auto;
    max-width: 100%;
}

.test {
    display:flex; z-index: 100; max-width: 100%; max-height: 100%; position: sticky;
}

.ausschlusskriterien-overlay {
    right: 0;
}

.search-tree-head {
    display: flex;
    flex-direction: row;
    box-sizing: border-box;
    place-content: stretch flex-start;
    align-items: stretch;
    border-top: 2px solid #adbcd7;
    border-bottom: 2px solid #adbcd7;
    overflow-x: auto;
}

.search-tree-button {
    display: flex;
    margin: auto;
}

.search-tree-button div {
    background-color: #ffffff;
    color: black;
    border: 1px solid #2e4884;
    border-radius: 5px;
    padding: 0.4em 0.5em;
    margin: 0.4em 0.5em;
    text-align: center;
    cursor: pointer;
    text-wrap: nowrap;
    min-width: 70px;
}

.search-tree-button div:active {
    background-color: #738cba;
    color: #ffffff;
}

.ontology-tree {
    max-height: 50vh;
    overflow-y: auto;
    height: 35vh;
    margin: 1em;
    font-family: Roboto, monospace !important;
}
</style>
