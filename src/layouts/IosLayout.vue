<template>
  <div class="q-pa-md">
    <q-layout
      view="lHh lpr lFf"
      container
      style="height: 95vh"
      class="shadow-2 rounded-borders"
    >
      <q-header
        bordered
        class="bg-white text-primary"
      >
        <q-toolbar>
          <q-toolbar-title class="text-center">
            Discoveryfy
          </q-toolbar-title>
          <q-btn
            @click="openLoginDialog"
            :ripple="{ center: true }"
            icon="account_box"
            push
            type="a"
          >
            {{ $t('Login') }}
          </q-btn>
        </q-toolbar>
      </q-header>

      <q-footer
        bordered
        class="bg-white text-primary"
        reveal
      >
        <q-tabs
          no-caps
          active-color="primary"
          indicator-color="transparent"
          class="text-grey"
        >
          <q-route-tab
            name="gavel"
            label="Polls"
            to="/polls"
          />
          <q-route-tab
            name="supervisor_account"
            label="Organizations"
            to="/orgs"
          />
        </q-tabs>
      </q-footer>

      <q-page-container>
        <router-view />
        <login-dialog
          ref="loginDialog"
          v-model="loginDialog"
        />
      </q-page-container>
    </q-layout>
  </div>
</template>

<script>
import LoginDialog from 'src/components/LoginDialog';

export default {
  name: 'IosLayout',
  components: {
    LoginDialog,
  },
  data() {
    return {
      loginDialog: false,
      tab: 'gavel',
    };
  },
  methods: {
    openLoginDialog() {
      this.$refs.loginDialog.open();
    }
  },
};
</script>
