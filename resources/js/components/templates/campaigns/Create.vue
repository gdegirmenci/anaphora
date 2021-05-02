<template>
  <v-dialog
    v-model="dialog"
    scrollable
    max-width="600px"
  >
    <template #activator="{ on, attrs }">
      <v-btn
        dark
        medium
        outlined
        v-bind="attrs"
        color="light-blue lighten-5"
        class="text-capitalize"
        v-on="on"
      >
        <v-icon>
          {{ createCampaignIcon }}
        </v-icon>
        {{ createCampaignText }}
      </v-btn>
    </template>
    <v-card>
      <v-toolbar
        flat
        color="light-blue darken-4"
        dark
      >
        {{ createCampaignText }}
      </v-toolbar>
      <v-card-text>
        <v-form
          ref="form"
          lazy-validation
        >
          <v-container>
            <v-row>
              <v-col
                cols="12"
                sm="12"
                md="12"
              >
                <v-text-field
                  v-model="campaign.name"
                  :rules="rules.text"
                  label="Campaign Name"
                  required
                />
              </v-col>
              <v-col
                cols="12"
                sm="12"
                md="12"
              >
                <v-text-field
                  v-model="campaign.subject"
                  :rules="rules.text"
                  label="Subject"
                  required
                />
              </v-col>
              <v-col
                cols="12"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.from.name"
                  :rules="rules.text"
                  label="From Name"
                  required
                />
              </v-col>
              <v-col
                cols="12"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.from.email"
                  :rules="rules.email"
                  label="From E-mail"
                  required
                />
              </v-col>
              <v-col
                cols="12"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.reply.name"
                  :rules="rules.text"
                  label="Reply Name"
                  required
                />
              </v-col>
              <v-col
                cols="12"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.reply.email"
                  :rules="rules.email"
                  label="Reply E-mail"
                  required
                />
              </v-col>
              <v-col
                cols="12"
                sm="12"
                md="12"
              >
                <v-textarea
                  v-model="campaign.template"
                  :rules="rules.text"
                  clearable
                  clear-icon="mdi-close-circle"
                  label="Content"
                  value=""
                />
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-spacer />
        <v-btn
          text
          large
          color="primary"
          @click="dialog = false"
        >
          Close
        </v-btn>
        <v-btn
          depressed
          large
          color="primary"
          @click="createCampaign"
        >
          Create
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>
</template>

<script>
import ApiEnums from '../../../enums/ApiEnums';

export default {
    name: 'Create',

    data () {
        return {
            dialog: false,
            createCampaignIcon: 'mdi-plus',
            createCampaignText: 'CREATE',
            rules: {
                text: [field => !!field || 'Field is required'],
                email: [
                    email => !!email || 'E-mail is required',
                    email => /.+@.+/.test(email) || 'E-mail must be valid',
                ],
            },
            campaign: {
                name: '',
                subject: '',
                from: { name: '', email: '' },
                reply: { name: '', email: '' },
                to: [{ name: '', email: '' }],
                template: ''
            }
        };
    },

    methods: {
        async createCampaign() {
            if (this.$refs.form.validate()) {
                this.dialog = false;

                await window.axios.post(ApiEnums.CREATE_CAMPAIGN_URL, {...this.campaign});
            }
        }
    }
};
</script>
