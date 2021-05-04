import Vue from 'vue';
import Vuex from 'vuex';
import { Mutation, Action } from './types';
import ApiEnums from '../enums/ApiEnums';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        appName: 'Anaphora',
        campaigns: {
            data: [],
            links: {},
            meta: { last_page: 1, per_page: 10 }
        },
        dashboard: { data: { overview: { queued: 0, sent: 0, failed: 0 }, providerStatus: [] } }
    },

    mutations: {
        /**
         * @param {object} state
         * @param {object} campaigns
         */
        [Mutation.SET_CAMPAIGNS](state, campaigns) {
            state.campaigns = campaigns;
        },

        /**
         * @param {object} state
         * @param {object} dashboard
         */
        [Mutation.SET_DASHBOARD](state, dashboard) {
            state.dashboard = dashboard;
        }
    },

    actions: {
        /**
         * @name fetchCampaigns
         * @param {function} [commit]
         * @param {object} pagination
         */
        async [Action.FETCH_CAMPAIGNS]({ commit }, pagination = { page: 1, itemsPerPage: 10 }) {
            const { data } = await window
                .axios
                .get(`${ApiEnums.FETCH_CAMPAIGNS_URL}?page=${pagination.page}&perPage=${pagination.itemsPerPage}`);

            commit(Mutation.SET_CAMPAIGNS, data);
        },

        /**
         * @name fetchDashboard
         * @param {function} [commit]
         * @param {number} page
         */
        async [Action.FETCH_DASHBOARD]({ commit }) {
            const { data } = await window.axios.get(ApiEnums.FETCH_DASHBOARD_URL);

            commit(Mutation.SET_DASHBOARD, data);
        }
    }
});
