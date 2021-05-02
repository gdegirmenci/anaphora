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
            v-for="(provider, index) in providerStatus"
            :key="index"
            :color="colors[provider.status]"
            :icon="'mdi-circle-slice-8'"
            :overline="provider.name"
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

    props: {
        /**
         * @property {array} providerStatus
         */
        providerStatus: {
            type: Array,
            required: true
        }
    },

    data () {
        return {
            title: 'Status',
            colors: { 'opened': 'red', 'half-opened': 'amber', 'closed': 'green' },
        };
    }
};
</script>
