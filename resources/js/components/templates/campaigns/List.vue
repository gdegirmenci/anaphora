<template>
  <div>
    <v-row>
      <div class="pa-3 text-h6">
        <span class="heading">
          {{ title }}
        </span>
      </div>
    </v-row>
    <v-row>
      <v-col col="3">
        <v-data-table
          outlined
          disable-sort
          :calculate-widths="true"
          :headers="headers"
          :items="campaigns.data"
          :item-key="title"
          :items-per-page="campaigns.meta.per_page"
          :server-items-length="totalCampaigns"
          :loading="loading"
          :footer-props="{ itemsPerPageOptions: [10, 20, 30] }"
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
                { text: 'Campaign ID', value: 'id', width: 50 },
                { text: 'Campaign Name', value: 'name', width: 200 },
                { text: 'Provider', value: 'provider', width: 100 },
                { text: 'Date', value: 'date', width: 200 },
                { text: 'Status', value: 'status', width: 100 },
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
            this.loading = false;
        }
    },

    methods: {
        /**
         * @param pagination
         */
        pagination(pagination) {
            this.loading = true;
            this.$emit('fetchCampaigns', pagination);
        }
    }
};
</script>
