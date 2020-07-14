<template>
  <div class="q-pa-md">
    <q-layout
      view="lHh lpr lFf"
      container
      style="height: 95vh"
      class="shadow-2 rounded-borders"
    >
      <q-header elevated>
        <q-toolbar>
          <q-toolbar-title
            class="text-center"
            to="/"
          >
            Discoveryfy
          </q-toolbar-title>
          <q-btn
            @click="openLoginDialog"
            :ripple="{ center: true }"
            icon="account_box"
            flat
            round
            dense
          />
        </q-toolbar>

        <q-tabs v-model="tab">
          <q-tab
            name="gavel"
            label="Polls"
          />
          <q-tab
            name="supervisor_account"
            label="Organizations"
          />
        </q-tabs>

        <q-toolbar>
          <q-toolbar-title
            class="text-center"
            to="/"
          >
            {{ this.$route.name }}
          </q-toolbar-title>
          <q-btn
            flat
            round
            dense
            icon="add"
          />
        </q-toolbar>
      </q-header>

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
  name: 'CardLayout',
  components: {
    LoginDialog,
  },
  data() {
    return {
      loginDialog: false,
    };
  },
  methods: {
    openLoginDialog() {
      this.$refs.loginDialog.open();
    }
  },
};
</script>
