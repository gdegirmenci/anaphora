<template>
  <v-app
    flat
    class="anaphora-app"
  >
    <navbar />
    <v-main>
      <v-container fluid>
        <router-view />
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import Navbar from './components/templates/Navbar';

export default {
    name: 'App',

    components: {
        Navbar
    }
};
</script>
