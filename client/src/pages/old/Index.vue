<template>
  <q-page>
    <!--  <q-page class="flex flex-center"><img alt="Quasar logo" src="~assets/quasar-logo-full.svg">-->
    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <h4 class="q-ma-sm col-12 text-weight-bold">
        Share & Rate Songs
      </h4>
      <h5 class="q-ma-sm col-12">
        Create collaborative playlists in Spotify with
        your friends and colleagues and rate the songs
      </h5>
    </div>

    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <q-btn
        push
        color="secondary"
        label="Create new organization"
        :ripple="{ center: true }"
        to="/organization/create"
      />
    </div>

    <cards />
  </q-page>
</template>

<style>
</style>

<script>
import cards from 'src/components/organization/Cards';

export default {
  name: 'Welcome',
  components: {
    cards,
  },
};
</script>
