import Vue from 'vue';
import VueRouter from 'vue-router';
import Campaigns from '../components/pages/Campaigns';
import Dashboard from '../components/pages/Dashboard';

Vue.use(VueRouter);

const routeIndex = [
    {
        name: 'dashboard',
        path: '/',
        component: Dashboard
    },
    {
        name: 'campaigns',
        path: '/campaigns',
        component: Campaigns
    }
];

export default new VueRouter({
    base: '/',
    mode: 'history',
    routes: routeIndex
});
