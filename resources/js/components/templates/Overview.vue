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
      <card-info
        v-for="(overviewCard, index) in overviewData"
        :key="index"
        :color="overviewCard.color"
        :icon="overviewCard.icon"
        :headline="overviewCard.headline"
        :overline="overviewCard.overline"
      />
    </v-row>
  </div>
</template>

<script>
import CardInfo from '../organism/CardInfo';

export default {
    name: 'Overview',

    components: {
        CardInfo
    },

    props: {
        /**
         * @property {object} overview
         */
        overview: {
            type: Object,
            required: true
        }
    },

    data () {
        return {
            title: 'Overview'
        };
    },

    computed: {
        /**
         *
         * @returns {array}
         */
        overviewData() {
            return [
                { overline: 'SENT', headline: this.overview.sent, color: 'teal', icon: 'mdi-check' },
                { overline: 'IN PROGRESS', headline: this.overview.queued, color: 'amber', icon: 'mdi-dots-horizontal' },
                { overline: 'FAILED', headline: this.overview.failed, color: 'deep-orange', icon: 'mdi-close' },
            ];
        }
    }
};
</script>
