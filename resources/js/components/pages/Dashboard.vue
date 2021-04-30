<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <overview></overview>
            </v-col>
            <v-col cols="12">
                <status></status>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import Overview from '../templates/Overview';
    import Status from '../templates/Status';

    export default {
        name: 'Dashboard',

        components: {
            Overview,
            Status
        }
    };
</script>
