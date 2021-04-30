<template>
    <v-container>
        <v-row>
            <v-col cols="12">
                <list></list>
            </v-col>
        </v-row>
    </v-container>
</template>

<script>
    import List from '../templates/campaigns/List';

    export default {
        name: 'Campaigns',

        components: {
            List
        }
    };
</script>
