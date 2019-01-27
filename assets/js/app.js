
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue/dist/bootstrap-vue.css'
import '../css/app.scss'

import Vue          from 'vue'
import VueRouter    from 'vue-router'
import VueClipboard from 'vue-clipboard2'
import BootstrapVue from 'bootstrap-vue'
import VueMoment from 'vue-moment'
import VueFeather from 'vue-feather';

Vue.use(BootstrapVue);
Vue.use(VueRouter);
Vue.use(VueClipboard)
Vue.use(VueMoment);
Vue.component(VueFeather.name, VueFeather);


import axios from 'axios'
import ErrorResponseHandler from './api/errorHandler'


import App     from './App.vue'
import router  from './router'
import store from './store'

new Vue({
    el: '#app',
    router: router,
    store: store,
    render: h => h(App)
});

axios.interceptors.response.use(
    response => { return response},
    ErrorResponseHandler
);

