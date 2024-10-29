import { Store } from 'vuex';
import { ComponentCustomProperties } from 'vue';

declare module 'vue/types/vue' {
    interface Vue {
      $store: Store<any>;
    }
  }