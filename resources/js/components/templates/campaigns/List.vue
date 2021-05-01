<template>
  <div>
    <v-row>
      <h2 class="pa-3 text-h6">
        {{ title }}
      </h2>
    </v-row>
    <v-row>
      <v-col col="3">
        <v-data-table
          outlined
          :headers="headers"
          :items="campaigns.data"
          :items-per-page="campaigns.meta.per_page"
          :server-items-length="totalCampaigns"
          :loading="loading"
          class="elevation-0"
          @pagination="pagination"
        >
          <template #[`item.status`]="{ item }">
            <v-chip
              class="lighten-5"
              :color="statusColors[item.status]"
            >
              {{ item.status }}
            </v-chip>
          </template>
        </v-data-table>
      </v-col>
    </v-row>
  </div>
</template>

<script>
export default {
    name: 'List',

    props: {
        /**
         * @property {object} campaigns
         */
        campaigns: {
            type: Object,
            required: true
        }
    },

    data () {
        return {
            title: 'Campaigns',
            headers: [
                { text: 'Name', value: 'name' },
                { text: 'To', value: 'to' },
                { text: 'Provider', value: 'provider' },
                { text: 'Date', value: 'date' },
                { text: 'Status', value: 'status' },
            ],
            statusColors: { 'Queue': 'amber', 'Sent': 'green', 'Failed': 'red' },
            loading: false
        };
    },

    computed: {
        /**
         * @returns {number}
         */
        totalCampaigns() {
            return this.campaigns.meta.per_page * this.campaigns.meta.last_page;
        }
    },

    watch: {
        campaigns: function () {
            console.log('changed');
            this.loading = false;
        }
    },

    methods: {
        /**
         * @param pagination
         */
        pagination(pagination) {
            this.loading = true;
            this.$emit('fetchCampaigns', pagination.page);
        }
    }
};
</script>
