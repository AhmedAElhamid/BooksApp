import Vue from "vue";
import Antd from 'ant-design-vue';
import 'ant-design-vue/dist/antd.css';


require('./bootstrap');
window.Vue = require('vue');

import App from './App.vue';
import VueRouter from 'vue-router';
import VueAxios from 'vue-axios';
import Vuex from "vuex";
import axios from 'axios';
import {routes} from './routes';
import store from './store'


Vue.use(VueRouter);
Vue.use(Antd);
Vue.use(VueAxios, axios);

const router = new VueRouter({
    mode: 'history',
    routes: routes
});

const app = new Vue({
    el: '#app',
    router: router,
    store,
    render: h => h(App),
});
