<template>
  <div class="d-flex flex-no-wrap justify-space-between align-center">
    <div>
      <v-card-title :class="alignPadding">
        <span class="heading">
          {{ overline }}
        </span>
      </v-card-title>
      <v-card-text
        v-if="hasHeadline"
        class="headline font-weight-bold"
      >
        <span class="heading">
          {{ headline }}
        </span>
      </v-card-text>
    </div>
    <div class="pr-5">
      <v-icon
        :color="color"
        class="material-icons-outlined"
      >
        {{ icon }}
      </v-icon>
    </div>
  </div>
</template>

<script>
export default {
    name: 'Info',

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
         * @property {boolean} hasHeadline
         */
        hasHeadline: {
            type: Boolean,
            default: true
        },
        /**
         * @property {number} headline
         */
        headline: {
            type: Number,
            default: 0
        },
        /**
         * @property {string} overline
         */
        overline: {
            type: String,
            required: true
        }
    },

    computed: {
        /**
         * @returns {string}
         */
        alignPadding() {
            const alignment = this.hasHeadline ? 'pb-0' : 'py-2';

            return 'overline font-weight-light ' + alignment;
        }
    }
};
</script>
