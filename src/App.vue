<template>
  <!--
	  SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	  SPDX-License-Identifier: AGPL-3.0-or-later
	  -->
  <div id="content" class="app-machbarkeit">
    <div id="container">
      <div id="content-left">
        <div id="attribute-list">
          <h2 style="font-weight: bold; text-align: center">Attributliste</h2>
          <input
            type="text"
            placeholder="Attribut suchen"
            style="width: 100%; margin-bottom: 15px; border-color: lightblue"
            v-model="txtSearch"
            @input="searchAttribute()"
          />
          <div style="overflow: auto">
            <div v-for="(modul, index) in modulName" :key="index">
              <a id="modul-name" @click="toggleExpansion(index)"
                >{{ modul }}
                <!-- :src (short for v-bind:src="") -->
                <img
                  style="width: 10px; height: 10px"
                  :src="
                    isExpanded(index)
                      ? 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-collapse.png'
                      : 'http://localhost:8080/apps-extra/machbarkeit/img/arrow-expand.png'
                  "
                />
              </a>
              <div style="margin: 10px 0px" v-show="isExpanded(index)">
                <!-- eslint-disable -->
                <div
                  id="attribute-items"
                  v-for="(item, key) in fillteredArr"
                  :key="key"
                  v-if="
                    item[
                      'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul'
                    ] === modul
                  "
                >
                  <!-- eslint-enable -->
                  <input
                    type="checkbox"
                    :id="key"
                    :value="
                      item[
                        'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name'
                      ]
                    "
                    v-model="selectedArr"
                    @change="showSelectedAttribute"
                  />
                  <!-- The for attribute is used in HTML to associate a <label> element with a form element -->
                  <label :for="key">{{
                    item[
                      "Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name"
                    ]
                  }}</label>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="selected-attribute">
          <h2 style="font-weight: bold; text-align: center">
            ausgewählte Attributliste
          </h2>
          <div style="overflow: auto">
            <div v-for="(modul, index) in selectedArrModulName" :key="index">
              <div id="modul-name">{{ modul }}</div>
              <!-- eslint-disable -->
              <div
                id="attribute-items"
                v-for="item in selectedAttribute"
                v-if="
                  item[
                    'Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul'
                  ] === modul
                "
              >
                <!-- eslint-enable -->
                <label style="cursor: auto">{{
                  item[
                    "Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name"
                  ]
                }}</label>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div id="content-right"></div>
    </div>
  </div>
</template>

<script>
import "@nextcloud/dialogs/styles/toast.scss";
import { generateUrl } from "@nextcloud/router";
import axios from "@nextcloud/axios";
import Papa from "papaparse";

export default {
  name: "App",
  data() {
    return {
      responseArray: [],
      fillteredArr: [],
      selectedArr: [],
      selectedAttribute: [],
      expandedGroup: [],
      modulName: [],
      selectedArrModulName: [],
      txtSearch: "",
    };
  },

  // life cycle of vue js
  // Call functions before all component are rendered
  beforeCreate() {},
  // Call functions before the template is rendered
  created() {
    this.loadcsv();
  },
  beforeMount() {},
  mounted() {},
  beforeUpdate() {},
  updated() {},
  beforeDestroy() {},
  destroyed() {},

  computed: {},
  /**
   * Fetch list of notes when the component is loaded
   */

  methods: {
    loadcsv() {
      const response = fetch(
        "http://localhost:8080/apps-extra/machbarkeit/csvfile/diz.csv"
      )
        .then((res) => res.text())
        .then((text) => Papa.parse(text, { header: true }))
        .catch((e) => console.error(e));
      response.then((v) => {
        // get all data from csv file
        this.responseArray = Object.values(v.data);

        this.responseArray = this.responseArray
          .filter(
            (item) =>
              item[
                "Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name"
              ]
          )
          .filter((attr) => attr !== "" && attr !== undefined);

        this.fillteredArr = this.responseArray;
        // get modul name
        this.modulName = this.getModulName(this.responseArray);
        // initialize keys from modulName.length (default: expand all attributelists)
        this.expandedGroup = [...Array(this.modulName.length).keys()];
      });
      return response;
    },

    isExpanded(key) {
      return this.expandedGroup.indexOf(key) !== -1;
    },

    toggleExpansion(key) {
      if (this.isExpanded(key)) {
        // .splice(start, deleteCount, item1, ..., itemN)
        this.expandedGroup.splice(this.expandedGroup.indexOf(key), 1);
      } else this.expandedGroup.push(key);
    },

    searchAttribute() {
      // delete empty string and undefined from attribute name
      this.responseArray = this.responseArray
        .filter(
          (item) =>
            item[
              "Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name"
            ]
        )
        .filter((attr) => attr !== "" && attr !== undefined);

      // filter txtSearch
      this.fillteredArr = this.responseArray.filter((item) =>
        item[
          "Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name"
        ]
          .toLowerCase()
          .includes(this.txtSearch.toLowerCase())
      );
    },

    getModulName(inputArr) {
      let name;
      name = inputArr.map(
        (item) =>
          item[
            "Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_kds_modul"
          ]
      );
      // filter duplicate
      name = name.filter(
        (item, index) =>
          name.indexOf(item) === index && item !== undefined && item !== ""
      );
      return name;
    },

    showSelectedAttribute() {
      this.selectedAttribute = this.responseArray.filter((filteredItem) =>
        this.selectedArr.includes(
          filteredItem[
            "Main.Daten.Metadaten.Metadata Repository.Code.Metadata RepositoryClass_attribut_name"
          ]
        )
      );
      this.selectedArrModulName = this.getModulName(this.selectedAttribute);
    },
  },
};
</script>
<style scoped>
#container {
  display: flex;
  flex-wrap: wrap;
  width: 100%;
  background-color: white;
}

#content-left {
  width: 25%;
  height: 90%;
  margin: 20px;
}

#attribute-list,
#content-left #selected-attribute {
  display: flex;
  flex-direction: column;

  height: 50%;
  background-color: aliceblue;
  border-radius: 26px;
  padding: 20px;
  margin-bottom: 20px;
}

#content-right {
  margin: 10px;
  width: 70%;
  height: 1000px;
}

#app-content > div {
  width: 100%;
  height: 100%;
  padding: 20px;
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

#toggle-img {
  width: 15px;
  height: 15px;
}

input[type="text"] {
  width: 100%;
}

textarea {
  flex-grow: 1;
  width: 100%;
}

#attribute-items {
  display: flex;
  flex-direction: row;
  column-gap: 8px;
  min-height: 30px;
  padding-left: 15px;
}

#attribute-items input {
  margin: 0px;
  position: relative;
  top: -6px;
}

#modul-name {
  font-weight: bold;
  margin-bottom: 2px;
}
</style>
