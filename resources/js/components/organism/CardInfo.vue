<template>
  <v-col
    cols="12"
    sm="12"
    md="4"
  >
    <v-card
      outlined
      class="mx-auto rounded"
    >
      <info
        :color="color"
        :overline="overline"
        :headline="headline"
        :icon="icon"
      />
    </v-card>
  </v-col>
</template>

<script>
import Info from './Info';

export default {
    name: 'CardInfo',

    components: {
        Info
    },

    props: {
        /**
         * @property {string} color
         */
        color: {
            type: String,
            default: 'indigo'
        },
        /**
         * @property {string} icon
         */
        icon: {
            type: String,
            required: true
        },
        /**
         * @property {number} headline
         */
        headline: {
            type: Number,
            required: true
        },
        /**
         * @property {string} overline
         */
        overline: {
            type: String,
            required: true
        }
    }
};
</script>
