require('./bootstrap');
import _ from 'lodash';

window.Vue = require('vue');
import router from './router'
import store from './store'
import ViewUI from 'view-design';
import 'view-design/dist/styles/iview.css';
Vue.use(ViewUI);

import { VueEditor } from "vue2-editor";
Vue.use(VueEditor);

import animateCss from 'animate.css';
Vue.use(animateCss);

//const dayjs = require('dayjs')
import dayjs from 'dayjs';
Vue.filter('formatDate', function(value) {
    if (value) {
       return dayjs(value).format('DD/MM/YYYY');
    }
})


import common from './common'
Vue.mixin(common);

Vue.component('mainapp', require('./components/mainapp.vue').default)

const app = new Vue({
    el: '#app',
    router,
    store
})

