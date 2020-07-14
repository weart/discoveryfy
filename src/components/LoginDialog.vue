<template>
  <q-dialog
    v-model="show"
    @cancel="close()"
    @escape-key="close()"
    transition-show="slide-up"
    transition-hide="slide-down"
  >
    <q-card
      square
      bordered
      class="shadow-1"
      style="width: 700px; max-width: 80vw;"
    >
      <q-card-section class="row items-center">
        <div class="text-h6">
          Login
        </div>
        <q-space />
        <q-btn
          icon="close"
          flat
          round
          dense
          v-close-popup
        />
      </q-card-section>
      <q-card-section>
        <q-form class="q-gutter-md">
          <q-input
            square
            filled
            clearable
            v-model="email"
            type="email"
            label="email"
          />
          <q-input
            square
            filled
            clearable
            v-model="password"
            type="password"
            label="password"
          />
        </q-form>
      </q-card-section>
      <q-card-actions class="q-px-md">
        <q-btn
          unelevated
          color="light-green-7"
          size="lg"
          class="full-width"
          label="Login"
          @click="doLogin"
        />
      </q-card-actions>
      <q-card-section class="text-center q-pa-none q-px-md">
        <p
          class="text-grey-6"
          @click="openRegisterDialog"
        >
          Not registered? Created an Account
        </p>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
import auth from 'src/utils/auth';

export default {
  name: 'LoginDialog',
  data () {
    return {
      email: '',
      password: '',
      show: false,
    }
  },
  methods: {
    close () {
      this.show = false;
    },
    open () {
      this.show = true;
    },
    openRegisterDialog() {
      this.close();
      this.$root.$emit('openRegisterDialog');
    },
    doLogin() {
      auth.login({
        email: this.email,
        password: this.password,
      }, '/profile');
    }
  }
}
</script>
