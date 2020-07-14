<template>
  <q-page>
    <!--<q-page class="flex flex-center">-->
    <!--<img alt="Quasar logo" src="~assets/quasar-logo-full.svg">-->
    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <h4 class="q-ma-sm col-12 text-weight-bold">
        Share & Rate Songs
      </h4>
    </div>

    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <q-btn
        push
        color="secondary"
        label="Create new poll"
        :ripple="{ center: true }"
        to="/polls/create"
      />
    </div>

    <cards :organization="organization" />
  </q-page>
</template>

<style>
</style>

<script>
import cards from 'src/components/poll/Cards';

export default {
  name: 'Polls',
  props: ['organization'],
  components: {
    cards,
  },
};
</script>
