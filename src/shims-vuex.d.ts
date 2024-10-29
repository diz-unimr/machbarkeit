/*
	SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
	SPDX-License-Identifier: AGPL-3.0-or-later
*/
import { Store } from 'vuex';
import { ComponentCustomProperties } from 'vue';

declare module 'vue/types/vue' {
    interface Vue {
      $store: Store<any>;
    }
  }