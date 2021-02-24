import Vue from "vue";
import App from "./App.vue";
import router from "./routes";
import store from "./store";
import { CONF } from './config';
import { utils } from './utils';


Vue.prototype.$utils = utils;
Vue.prototype.$conf = CONF;

require('./bootstrap/core')
require('./bootstrap/axios')
require('./bootstrap/form')
require('./bootstrap/table')

new Vue({ 
  store,
  router,
  render: h => h(App) 
}).$mount("#app");
