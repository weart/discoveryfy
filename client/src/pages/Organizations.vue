<template>
  <q-page>
    <!--<q-page class="flex flex-center">-->

    <q-ajax-bar
      ref="bar"
      position="bottom"
      color="primary"
      size="10px"
      skip-hijack
    />

    <div class="row q-pa-lg q-gutter-none justify-around full-width text-center">
      <h5 class="q-ma-sm col-12 text-weight-bold">
        Welcome to Discoveryfy
      </h5>
      <span class="q-ma-sm col-12">Share & Rate Songs</span>
      <span class="q-ma-sm col-12">
        Create collaborative playlists in Spotify with
        your friends and colleagues and rate the songs
      </span>
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

    <div class="row q-pa-md q-gutter-md justify-around items-start">
      <q-spinner-audio
        v-if="isLoading"
        color="primary"
        size="2em"
      />

      <template v-if="!isLoading">
        <h6 class="full-width text-center q-ma-xs">
          Organizations
        </h6>

        <organizationCard
          v-for="organization in items"
          :key="organization.id"
          :organization="organization"
        />
      </template>
    </div>
  </q-page>
</template>

<style>
</style>

<script>
import { list } from 'src/utils/vuexer';
import ListMixin from 'src/common/mixins/ListMixin';
const servicePrefix = 'Organization';
const { getters, actions } = list(servicePrefix);

import organizationCard from 'src/components/OrganizationCard';

export default {
  name: 'Organizations',
  servicePrefix,
  mixins: [ListMixin],
  components: {
    organizationCard,
  },
  computed: getters,
  methods: actions,
  watch: {
    isLoading(val) {
      if (val) {
        this.$refs.bar.start();
      } else {
        this.$refs.bar.stop();
      }
    },
  }
};
</script>
