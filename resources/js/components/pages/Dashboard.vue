<template>
  <v-container>
    <v-row>
      <v-col cols="12">
        <overview :overview="dashboard.data.overview" />
      </v-col>
      <v-col cols="12">
        <status :provider-status="dashboard.data.providerStatus" />
      </v-col>
    </v-row>
  </v-container>
</template>

<script>
import { mapState, mapActions } from 'vuex';
import Overview from '../templates/Overview';
import Status from '../templates/Status';

export default {
    name: 'Dashboard',

    components: {
        Overview,
        Status
    },

    computed: {
        ...mapState(['dashboard'])
    },

    beforeMount() {
        this.fetchDashboard();
    },

    methods: {
        ...mapActions(['fetchDashboard'])
    }
};
</script>
