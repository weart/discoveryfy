<template>
  <q-page>
    <!--<q-ajax-bar ref="bar" position="top" color="accent" size="10px" skip-hijack />-->
    <q-toolbar class="q-my-md" /><!-- spacer -->

    <div
      class="row q-pa-md q-gutter-md justify-around items-start"
      v-if="isLoading"
    >
      <q-spinner-audio
        v-if="isLoading"
        color="primary"
        size="2em"
      />
    </div>

    <template v-if="!isLoading && item">
      <q-card
        flat
        bordered
        class="center-card"
      >
        <q-toolbar>
          <q-card-section class="card-title">
            <div>Properties</div>
          </q-card-section>
          <q-space />
          <q-btn
            flat
            rounded
            icon="delete_forever"
            color="warning"
            label="Delete poll"
          >
            <q-tooltip>Delete group</q-tooltip>
          </q-btn>
          <q-btn
            flat
            rounded
            icon="save"
            label="Save changes"
          >
            <q-tooltip>Save changes</q-tooltip>
          </q-btn>
        </q-toolbar>

        <q-separator />

        <q-card-section>
          <q-form>
            <q-tabs
              v-model="tab"
              dense
              class="text-grey"
              active-color="primary"
              indicator-color="primary"
              align="justify"
              narrow-indicator
            >
              <q-tab
                name="basic"
                label="Basic"
              />
              <q-tab
                name="password"
                label="Change password"
              />
              <q-tab
                name="advanced"
                label="Advanced"
              />
            </q-tabs>

            <q-separator />

            <q-tab-panels
              v-model="tab"
              animated
            >
              <q-tab-panel name="basic">
                <q-toggle
                  label="Readonly"
                  v-model="readonly"
                  color="primary"
                  keep-color
                />
                <q-toggle
                  label="Disable"
                  v-model="disable"
                  color="primary"
                  keep-color
                />
                <q-input
                  label="id"
                  v-model="item.id"
                  readonly
                  disable
                />
                <span>{{ formatDateTime(item.createdAt, 'long') }}</span>
                <q-input
                  label="created at"
                  v-model="item.createdAt"
                  readonly
                  disable
                />
                <span>{{ formatDateTime(item.updatedAt, 'long') }}</span>
                <q-input
                  label="updated at"
                  v-model="item.updatedAt"
                  readonly
                  disable
                />
                <q-input
                  label="name"
                  v-model="item.username"
                  :readonly="readonly"
                  :disable="disable"
                />
              </q-tab-panel>
              <q-tab-panel name="password">
                <q-input
                  label="Current password"
                  v-model="old_password"
                  :readonly="readonly"
                  :disable="disable"
                />
                <q-input
                  label="New password"
                  v-model="new_password"
                  :readonly="readonly"
                  :disable="disable"
                />
                <q-btn
                  flat
                  rounded
                  icon="save"
                  color="warning"
                  label="Change password"
                />
              </q-tab-panel>
              <q-tab-panel name="advanced">
                <q-list>
                  <q-item>
                    <q-item-section avatar>
                      <q-item-label>
                        Can your username be seen be anyone?
                      </q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-btn-toggle
                        v-model="item.publicVisibility"
                        spread
                        rounded
                        no-caps
                        :readonly="readonly"
                        :disable="disable"
                        :options="[
                          {label: 'Yes', value: true},
                          {label: 'No', value: false},
                        ]"
                      />
                    </q-item-section>
                  </q-item>
                  <q-item>
                    <q-item-section avatar>
                      <q-item-label>
                        Can you be searched by your email?
                      </q-item-label>
                      <q-item-label caption>
                        Otherwise the only method for add you to groups is with your user id
                      </q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-btn-toggle
                        v-model="item.publicEmail"
                        spread
                        rounded
                        no-caps
                        :readonly="readonly"
                        :disable="disable"
                        :options="[
                          {label: 'Yes', value: true},
                          {label: 'No', value: false},
                        ]"
                      />
                    </q-item-section>
                  </q-item>
                  <q-item>
                    <q-item-section avatar>
                      <q-item-label>
                        Language
                      </q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-select
                        v-model="item.language"
                        :options="[
                          {label: 'English', value: 'en'},
                          {label: 'Català', value: 'ca'},
                        ]"
                      />
                    </q-item-section>
                  </q-item>
                  <q-item>
                    <q-item-section avatar>
                      <q-item-label>
                        Theme
                      </q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-select
                        v-model="item.theme"
                        :options="[
                          {label: 'Default', value: 'default'},
                        ]"
                      />
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-tab-panel>
            </q-tab-panels>
          </q-form>
        </q-card-section>
      </q-card>
    </template>
  </q-page>
</template>

<script>
import { show } from 'src/utils/vuexer';
import ShowMixin from 'src/common/mixins/ShowMixin';
const servicePrefix = 'User';
const { getters, actions } = show(servicePrefix);

export default {
  name: 'User',
  servicePrefix,
  mixins: [ShowMixin],
  data() {
    return {
      tab: 'basic',
      readonly: false,
      disable: false,
      old_password: '',
      new_password: '',
    };
  },
  computed: {
    ...getters
  },
  methods: {
    ...actions
  },
};
</script>
