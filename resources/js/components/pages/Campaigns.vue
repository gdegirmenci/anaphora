<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <list
          :campaigns="campaigns"
          @fetchCampaigns="fetchCampaigns"
        />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import List from '../templates/campaigns/List';

export default {
    name: 'Campaigns',

    components: {
        List
    },

    computed: {
        ...mapState(['campaigns'])
    },

    async beforeMount() {
        await this.fetchCampaigns();
    },

    methods: {
        ...mapActions(['fetchCampaigns'])
    }
};
</script>
