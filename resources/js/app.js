require('./bootstrap');

import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import vuetify from './vuetify';

Vue.router = router;
Vue.axios = window.axios;

new Vue({
    store,
    router,
    vuetify,
    render: h => h(App)
}).$mount('#app');
