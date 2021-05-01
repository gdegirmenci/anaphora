<template>
  <div>
    <v-row>
      <h2 class="pa-3 text-h6">
        {{ title }}
      </h2>
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

    data () {
        return {
            title: 'Overview',
            overviewData: [
                { overline: 'SENT', headline: 23, color: 'teal', icon: 'mdi-check' },
                { overline: 'IN PROGRESS', headline: 3, color: 'amber', icon: 'mdi-dots-horizontal' },
                { overline: 'FAILED', headline: 5, color: 'deep-orange', icon: 'mdi-close' },
            ]
        };
    }
};
</script>
