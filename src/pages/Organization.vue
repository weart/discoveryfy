<template>
  <q-page>
    <!--<q-ajax-bar ref="bar" position="top" color="accent" size="10px" skip-hijack />-->

    <q-toolbar class="q-my-md">
      <q-breadcrumbs class="q-mr-sm">
        <q-breadcrumbs-el
          v-for="(breadcrumb, idx) in $route.meta.breadcrumb"
          :key="idx"
          :label="breadcrumb.label"
          :icon="breadcrumb.icon"
          :to="breadcrumb.to"
        />
        <q-breadcrumbs-el
          v-if="item"
          :label="item.name"
        />
      </q-breadcrumbs>
      <q-space />
      <q-btn
        flat
        round
        dense
        icon="add"
        @click="organizationCreate"
      />
    </q-toolbar>

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
            <div>Playlists</div>
          </q-card-section>
          <q-space />
          <q-btn
            flat
            rounded
            icon="add"
            label="Create new playlist"
          />
        </q-toolbar>

        <q-separator />

        <q-card-section
          style="max-height: 50vh"
          class="scroll"
        >
          <template v-if="!item || !item.polls || item.polls.length == 0">
            <h5>Without polls</h5>
          </template>
          <template v-if="item && item.polls.length > 0">
            <!--<tracks-list :poll="item" />-->
            <!--<pollCard
              v-for="poll in item.polls"
              :key="poll.id"
              :poll="poll"
            />-->
            <pollTableGrid :items="item.polls" />
          </template>
        </q-card-section>
      </q-card>

      <br><br>

      <q-card
        flat
        bordered
        class="center-card"
      >
        <q-toolbar>
          <q-card-section class="card-title">
            <div>Members</div>
          </q-card-section>
          <q-space />
          <q-btn
            flat
            rounded
            icon="add"
            label="Invite new members"
          />
        </q-toolbar>

        <q-separator />

        <q-card-section
          style="max-height: 50vh"
          class="scroll"
        >
          <template v-if="!item || !item.memberships || item.memberships.length == 0">
            <h5>Without members</h5>
          </template>
          <template v-if="item && item.memberships.length > 0">
            <members-list :organization="item" />
          </template>
        </q-card-section>
      </q-card>

      <br><br>

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

        <q-card-section
          style="max-height: 50vh"
          class="scroll"
        >
          <q-form>
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
            />
            <q-input
              label="name"
              v-model="item.name"
              :readonly="readonly"
              :disable="disable"
            />

            <br>
            <br>

            <q-list>
              <q-item>
                <q-item-section avatar>
                  <q-item-label>
                    Is this organization public and can be seen be anyone?
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
                    Can anyone be part of this organization? Or an invitation is required?
                  </q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-btn-toggle
                    v-model="item.publicMembership"
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
                    Minimum membership rol for allow create new polls
                  </q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-btn-toggle
                    v-model="item.canCreatePolls"
                    spread
                    rounded
                    no-caps
                    :readonly="readonly"
                    :disable="disable"
                    :options="[
                      {label: 'Owner', value: 0},
                      {label: 'Admin', value: 1},
                      {label: 'Members', value: 2},
                      {label: 'Anyone', value: 4},
                    ]"
                  />
                </q-item-section>
              </q-item>
              <q-item v-if="!readonly && !disable">
                <q-item-section avatar>
                  <q-item-label>
                    Invite new member
                  </q-item-label>
                </q-item-section>
                <q-item-section>
                  <q-input
                    label="id"
                    v-model="invite"
                  />
                </q-item-section>
                <q-item-section thumbnail>
                  <q-btn
                    flat
                    rounded
                    icon="send"
                    label="Send"
                  >
                    <!--icon: mail_outline-->
                    <q-tooltip>Send invitation</q-tooltip>
                  </q-btn>
                </q-item-section>
              </q-item>
            </q-list>
          </q-form>
        </q-card-section>
      </q-card>
    </template>
  </q-page>
</template>

<script>
import { show } from 'src/utils/vuexer';
import ShowMixin from 'src/common/mixins/ShowMixin';
const servicePrefix = 'Organization';
const { getters, actions } = show(servicePrefix);
// import tracksList from 'src/components/TracksList';
// import pollCard from 'src/components/PollCard';
// import pollTableList from 'src/components/PollTableList';
import pollTableGrid from 'src/components/PollTableGrid';
import membersList from 'src/components/MembersList';

export default {
  name: 'Organization',
  servicePrefix,
  mixins: [ShowMixin],
  components: {
    // tracksList,
    // pollCard,
    // pollTableList,
    pollTableGrid,
    membersList,
  },
  data() {
    return {
      readonly: false,
      disable: false,
      invite: '',
    };
  },
  computed: {
    ...getters
  },
  methods: {
    ...actions,
    organizationCreate() {
      console.log('@todo org create');
      // put /orgs & go
      // this.$router.push({ name: 'Organization', params: { id: this.organization.id } });
    },
  },
};
</script>
