<template>
  <div>
    <v-row>
      <h2 class="pa-3 text-h6">
        {{ title }}
      </h2>
    </v-row>
    <v-row>
      <v-col cols="12">
        <v-card
          outlined
          class="mx-auto rounded"
        >
          <info
            v-for="(status, index) in statuses"
            :key="index"
            :color="colors[status.circleStatus]"
            :icon="'mdi-circle-slice-8'"
            :overline="status.providerName"
            :has-headline="false"
          />
        </v-card>
      </v-col>
    </v-row>
  </div>
</template>

<script>
import Info from '../organism/Info';

export default {
    name: 'Status',

    components: {
        Info
    },

    data () {
        return {
            title: 'Status',
            colors: { 'open': 'red', 'half-open': 'amber', 'close': 'green' },
            statuses: [
                { providerName: 'SENDGRID', circleStatus: 'close' },
                { providerName: 'MAILJET', circleStatus: 'close' },
            ],
        };
    }
};
</script>
