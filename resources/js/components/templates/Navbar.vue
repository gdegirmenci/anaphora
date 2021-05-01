<template>
  <v-app-bar
    app
    flat
    color="light-blue darken-4"
    dark
  >
    <v-container class="py-0 fill-height">
      <v-toolbar-title class="font-weight-bold text-subtitle-1 mr-10">
        {{ title }}
      </v-toolbar-title>
      <v-btn
        text
        to="/"
        class="mr-2"
      >
        Dashboard
      </v-btn>
      <v-btn
        text
        to="/campaigns"
      >
        Campaigns
      </v-btn>
      <v-spacer />
    </v-container>
  </v-app-bar>
</template>

<script>
export default {
    name: 'Navbar',

    data () {
        return {
            title: 'ANAPHORA'
        };
    }
};
</script>
