<template>
  <q-layout view="hHh lpR fFf">
    <q-header elevated>
      <q-toolbar>
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
        <q-toolbar-title
          class="text-center"
          to="/"
        >
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
      <!--
      <q-tabs inline-label class="bg-primary text-white shadow-2">
        <q-route-tab to="/polls?active=1" label="Current polls" icon="gavel">
          <q-badge color="orange" floating>20</q-badge>
        </q-route-tab>
        <q-route-tab to="/polls?active=0" label="Past polls" icon="watch_later" />
      </q-tabs>
      -->
    </q-header>

    <q-page-container>
      <router-view />
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
import LoginDialog from 'src/components/LoginDialog';
import RegisterDialog from "../components/RegisterDialog";
import auth from 'src/utils/auth';

export default {
  name: 'MyLayout',
  components: {
    LoginDialog,
    RegisterDialog,
  },
  data() {
    return {
      isAuthenticated: false,
      loginDialog: false,
      registerDialog: false,
    };
  },
  mounted() {
    this.$root.$on('openLoginDialog', this.openLoginDialog);
    this.$root.$on('openRegisterDialog', this.openRegisterDialog);
  },
  beforeDestroy() {
    this.$root.$off(['openLoginDialog','openRegisterDialog']);
  },
  methods: {
    checkAuth() {
      auth.checkAuth(this);
      this.isAuthenticated = auth.user.authenticated;
    },
    openLoginDialog() {
      this.$refs.loginDialog.open();
    },
    openRegisterDialog() {
      this.$refs.registerDialog.open();
    }
  },
};
</script>
