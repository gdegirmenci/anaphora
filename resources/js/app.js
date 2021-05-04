require('./bootstrap');

import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import vuetify from './vuetify';
import Vue2Editor from 'vue2-editor';

Vue.router = router;
Vue.axios = window.axios;
Vue.use(Vue2Editor);

new Vue({
    store,
    router,
    vuetify,
    render: h => h(App)
}).$mount('#app');
