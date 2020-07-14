<template>
  <q-layout
    view="lHh Lpr lFf"
    class="bg-white"
  >
    <q-header elevated>
      <q-toolbar>
        <template v-if="organizations.length > 0">
          <q-btn
            flat
            dense
            round
            @click="leftDrawerOpen = !leftDrawerOpen"
            aria-label="Menu"
            icon="menu"
          />
        </template>
        <q-btn
          color="primary"
          icon="gavel"
          label="Polls"
          to="/polls"
        />
        <q-btn
          icon="supervisor_account"
          label="Groups"
          to="/orgs"
        />
        <q-space />
        <q-toolbar-title>
          Discoveryfy: {{ this.$route.name }}
        </q-toolbar-title>
        <q-space />

        <template v-if="isAuthenticated">
          <q-btn
            to="/user"
            :ripple="{ center: true }"
            icon="account_box"
          >
            {{ $t('Profile') }}
          </q-btn>
        </template>
        <template v-else>
          <q-btn
            @click="openLoginDialog"
            :ripple="{ center: true }"
            icon="account_box"
          >
            {{ $t('Login') }}
          </q-btn>
        </template>
      </q-toolbar>
    </q-header>

    <template v-if="organizations.length > 0">
      <q-drawer
        v-model="leftDrawerOpen"
        show-if-above
        bordered
        content-class="bg-grey-2"
      >
        <q-list>
          <q-expansion-item
            v-for="organization in organizations"
            :key="organization.id"
            :label="`${organization.name} Playlists`"
            :to="`/org/${organization.id}`"
            default-opened
            expand-separator
            expand-icon-toggle
          >
            <q-item
              v-for="poll in itemsFilterOrgId(organization.id)"
              :key="poll.id"
              tag="a"
              v-ripple
              :to="{ name: 'Poll', params: { id: poll.id } }"
            >
              <q-item-section avatar>
                <q-icon name="school" />
              </q-item-section>
              <q-item-section>
                <q-item-label>{{ poll.name }}</q-item-label>
                <q-item-label caption>
                  {{ poll.description }}
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-expansion-item>
          <!--
          <q-expansion-item
            label="Sonosuite"
            default-opened
            expand-separator
            expand-icon-toggle
            to="/org/13959f26-d6ee-48cc-8e7c-ee4dd26b06be"
          >
            <q-item
              clickable
              tag="a"
              to="/poll/c8b38746-edb9-43b7-bffc-f9ff5222a6a4"
            >
              <q-item-section avatar>
                <q-icon name="school" />
              </q-item-section>
              <q-item-section>
                <q-item-label>Sonosuite Weekly Playlist</q-item-label>
                <q-item-label caption>
                  Public playlist restarted weekly
                </q-item-label>
              </q-item-section>
            </q-item>
          </q-expansion-item>
          -->
        </q-list>
      </q-drawer>
    </template>

    <q-page-container>
      <router-view
        :key="$route.fullPath"
      />
    </q-page-container>

    <login-dialog
      ref="loginDialog"
      v-model="loginDialog"
    />
    <register-dialog
      ref="registerDialog"
      v-model="registerDialog"
    />
  </q-layout>
</template>

<script>
import { list } from 'src/utils/vuexer';
import ListMixin from 'src/common/mixins/ListMixin';
const servicePrefix = 'Poll';
const { getters, actions } = list(servicePrefix);
import LoginDialog from 'src/components/LoginDialog';
import RegisterDialog from "../components/RegisterDialog";
import auth from 'src/utils/auth';

export default {
  name: 'DrawerToolbarLayout',
  servicePrefix,
  mixins: [ListMixin],
  components: {
      LoginDialog,
      RegisterDialog,
  },
  data() {
    return {
      isAuthenticated: false,
      loginDialog: false,
      registerDialog: false,
      leftDrawerOpen: false,
      pagination: {
        sortBy: 'endDate',
        descending: false,
        page: 1, // page to be displayed
        rowsPerPage: 99, // maximum displayed rows
        rowsNumber: 99, // virtualy the max number of rows
      },
    };
  },
  mounted() {
    this.$root.$on('openLoginDialog', this.openLoginDialog);
    this.$root.$on('openRegisterDialog', this.openRegisterDialog);
  },
  beforeDestroy() {
    this.$root.$off(['openLoginDialog','openRegisterDialog']);
  },
  computed: {
    ...getters,
    organizations () {
      if (_.size(this.items) <1) return [];
      return _.uniqBy(_.map(this.items,'organization'),'id');
    },
  },
  methods: {
    ...actions,
    checkAuth() {
      auth.checkAuth(this);
      this.isAuthenticated = auth.user.authenticated;
    },
    openLoginDialog() {
      this.$refs.loginDialog.open();
    },
    openRegisterDialog() {
      this.$refs.registerDialog.open();
    },
    itemsFilterOrgId(id) {
      return _.reject(this.items, function(item) { return item.organization.id != id; });
    }
  },
}
</script>
