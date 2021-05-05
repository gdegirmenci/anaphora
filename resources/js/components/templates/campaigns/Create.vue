<template>
  <v-dialog
    v-model="dialog"
    scrollable
    fullscreen
    hide-overlay
    transition="dialog-bottom-transition"
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
        <span class="d-none d-sm-flex">
          {{ createCampaignText }}
        </span>
      </v-btn>
    </template>
    <v-card>
      <v-toolbar
        max-height="64"
        flat
        color="light-blue darken-4"
        dark
      >
        <v-btn
          icon
          dark
          @click="dialog = false"
        >
          <v-icon>mdi-close</v-icon>
        </v-btn>
        <v-toolbar-title class="text-subtitle-2 ml-2">
          <span class="heading">
            {{ createCampaignText }} CAMPAIGN
          </span>
        </v-toolbar-title>
      </v-toolbar>
      <v-card-text>
        <v-form
          ref="form"
          lazy-validation
        >
          <v-container>
            <v-row class="mt-5">
              <v-col
                cols="6"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.name"
                  outlined
                  :rules="rules.text"
                  label="Campaign Name"
                  required
                />
              </v-col>
              <v-col
                cols="6"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.subject"
                  outlined
                  :rules="rules.text"
                  label="Subject"
                  required
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="6"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.from.name"
                  outlined
                  :rules="rules.text"
                  label="From Name"
                  required
                />
              </v-col>
              <v-col
                cols="6"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.from.email"
                  outlined
                  :rules="rules.email"
                  label="From E-mail"
                  required
                />
              </v-col>
            </v-row>
            <v-row>
              <v-col
                cols="6"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.reply.name"
                  outlined
                  :rules="rules.text"
                  label="Reply Name"
                  required
                />
              </v-col>
              <v-col
                cols="6"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="campaign.reply.email"
                  outlined
                  :rules="rules.email"
                  label="Reply E-mail"
                  required
                />
              </v-col>
            </v-row>
            <v-row
              v-for="(to, index) in campaign.to"
              :key="index"
            >
              <v-col
                cols="6"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="to.name"
                  outlined
                  :rules="rules.text"
                  label="Recipient Name"
                  required
                />
              </v-col>
              <v-col
                cols="6"
                sm="6"
                md="6"
              >
                <v-text-field
                  v-model="to.email"
                  outlined
                  :append-icon="appendIcon()"
                  :append-outer-icon="appendOuterIcon(index)"
                  :rules="rules.email"
                  label="Recipient E-mail"
                  required
                  @click:append-outer="addRecipient"
                  @click:append="removeRecipient(index)"
                />
              </v-col>
            </v-row>
            <v-row class="mt-0">
              <v-row justify="space-between">
                <v-col
                  cols="1"
                  class="mt-3"
                >
                  <v-subheader class="subtitle-1">
                    <span class="heading">
                      Content
                    </span>
                  </v-subheader>
                </v-col>
                <v-col
                  cols="auto"
                  class="mr-7"
                >
                  <v-switch
                    v-model="editor"
                    inset
                    :label="`Editor`"
                    @change="updateType"
                  />
                </v-col>
              </v-row>
              <v-col
                cols="12"
                sm="12"
                md="12"
              >
                <vue-editor
                  v-if="editor"
                  v-model="campaign.template"
                  :use-markdown-shortcuts="true"
                  :editor-toolbar="toolbar"
                />
                <v-textarea
                  v-if="!editor"
                  v-model="campaign.template"
                  outlined
                  auto-grow
                  :rules="rules.text"
                  clearable
                  clear-icon="mdi-close-circle"
                />
              </v-col>
            </v-row>
          </v-container>
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-container>
          <v-row>
            <v-col
              cols="12"
              sm="6"
            >
              <v-btn
                text
                block
                large
                color="primary"
                @click="dialog = false"
              >
                Close
              </v-btn>
            </v-col>
            <v-col
              cols="12"
              sm="6"
            >
              <v-btn
                depressed
                block
                large
                color="primary"
                @click="createCampaign"
              >
                Create
              </v-btn>
            </v-col>
          </v-row>
        </v-container>
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
            editor: true,
            createCampaignIcon: 'mdi-plus',
            createCampaignText: 'CREATE',
            rules: {
                text: [field => !!field || 'Field is required'],
                email: [
                    email => !!email || 'E-mail is required',
                    email => /.+@.+/.test(email) || 'E-mail must be valid',
                ],
            },
            toolbar: [
                ['bold', 'italic', 'underline', 'strike'],
                [{ color: [] }, { background: [] }],
                [{ font: [] }],
                [{ header: [false, 1, 2, 3, 4, 5, 6] }],
                [
                    { align: '' },
                    { align: 'center' },
                    { align: 'right' },
                    { align: 'justify' }
                ],
                ['blockquote', 'code-block'],
                [{ list: 'ordered' }, { list: 'bullet' }, { list: 'check' }],
                [{ indent: '-1' }, { indent: '+1' }],
                ['link'],
                [{ direction: 'rtl' }],
                ['clean']
            ],
            campaign: {
                name: '',
                subject: '',
                from: { name: '', email: '' },
                reply: { name: '', email: '' },
                to: [{ email: '', name: '' }],
                template: '',
                type: 'html'
            }
        };
    },

    methods: {
        /**
         * @returns {void}
         */
        async createCampaign() {
            if (this.$refs.form.validate()) {
                this.dialog = false;

                await window.axios.post(ApiEnums.CREATE_CAMPAIGN_URL, {...this.campaign});
            }
        },

        /**
         * @returns {void}
         */
        addRecipient() {
            this.campaign.to.push({ email: '', name: '' });
        },

        /**
         * @returns {void}
         */
        removeRecipient(index) {
            this.campaign.to.splice(index, 1);
        },

        /**
         * @returns {string}
         */
        appendIcon() {
            return this.campaign.to.length !== 1 ? 'mdi-minus' : '';
        },

        /**
         * @param {number} index
         * @returns {string}
         */
        appendOuterIcon(index) {
            return this.campaign.to.length === index + 1 ? 'mdi-plus' : '';
        },

        /**
         * @returns {void}
         */
        updateType() {
            this.campaign.type = this.editor ? 'html' : 'text';
        }
    }
};
</script>
