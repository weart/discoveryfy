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
          Register
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

          <br>

          <q-list>
            <q-item>
              <q-item-section avatar>
                <q-item-label>
                  Can your username be seen be anyone?
                </q-item-label>
              </q-item-section>
              <q-item-section>
                <q-btn-toggle
                  v-model="publicVisibility"
                  spread
                  rounded
                  no-caps
                  :options="[
                    {label: 'Yes', value: true},
                    {label: 'No', value: false},
                  ]"
                />
              </q-item-section>
            </q-item>
            <q-item>
              <q-item-section>
                <q-item-label>
                  Can you be searched by your email?
                </q-item-label>
                <q-item-label caption>
                  Otherwise the only method for add you to groups is with your user id
                </q-item-label>
              </q-item-section>
              <q-item-section>
                <q-btn-toggle
                  v-model="publicEmail"
                  spread
                  rounded
                  no-caps
                  :options="[
                    {label: 'Yes', value: true},
                    {label: 'No', value: false},
                  ]"
                />
              </q-item-section>
            </q-item>
          </q-list>
        </q-form>
      </q-card-section>
      <q-card-actions class="q-px-md">
        <q-btn
          unelevated
          color="light-green-7"
          size="lg"
          class="full-width"
          label="Register"
          v-close-popup
        />
      </q-card-actions>
      <q-card-section class="text-center q-pa-none q-px-md">
        <p
          class="text-grey-6"
          @click="openLoginDialog"
        >
          Already registered?
        </p>
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'RegisterDialog',
  data () {
    return {
      email: '',
      password: '',
      publicVisibility: false,
      publicEmail: false,
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
    openLoginDialog() {
      this.close();
      this.$root.$emit('openLoginDialog');
    },
  }
}
</script>
