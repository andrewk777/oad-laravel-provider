import Vue from 'vue';
import axios from 'axios';
import VueAxios from 'vue-axios';
import store from "@/store";
import { CONF } from '@/config';
import { logout } from '@/auth';
import { utils } from '@/utils';

Vue.use(VueAxios, axios)

window.axios = require('axios');
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true;
axios.defaults.headers.common['Authorization'] = 'Bearer ' + store.state.accessToken;
axios.defaults.baseURL = CONF.API_URL


axios.interceptors.response.use(
    res => {
        if (res.status < 400 && res.status != 251) {
            return res
        } else if (res.status == 251) { //permission managment
            utils.c_notify(res.data.msg, res.data.status);
            return false;
        }
    }, 
    error => {
        if (error.response.status >= 500) {
            utils.c_notify('System Error', 'error');
        } else if (error.response.status = 401) {
            if (store.state.showSesExp) {
                utils.c_notify('Session Expired', 'error');
                store.commit('setShowSesExp', false);
            }
            logout()  
        }
    }
  );