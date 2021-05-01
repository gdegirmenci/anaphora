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
    },

    mutations: {
        /**
         * @param {object} state
         * @param {object} campaigns
         */
        [Mutation.SET_CAMPAIGNS](state, campaigns) {
            state.campaigns = campaigns;
        }
    },

    actions: {
        /**
         * @name fetchCampaigns
         * @param {function} [commit]
         * @param {number} page
         */
        async [Action.FETCH_CAMPAIGNS]({ commit }, page = 1) {
            const { data } = await window.axios.get(`${ApiEnums.FETCH_CAMPAIGNS_URL}?page=${page}`);

            commit(Mutation.SET_CAMPAIGNS, data);
        }
    }
});
