<template>
    <div>
        <v-row>
            <h2 class="pa-3 text-h6">{{ title }}</h2>
        </v-row>
    </div>
</template>

<script>
    export default {
        name: 'List',

        data () {
            return {
                title: 'Campaigns'
            }
        },
    };
</script>
