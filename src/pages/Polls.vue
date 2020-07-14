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
        label="Create new poll"
        :ripple="{ center: true }"
        to="/polls/create"
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
          Polls
        </h6>

        <pollCard
          v-for="poll in items"
          :key="poll.id"
          :poll="poll"
        />

        <!--
        <pollCard
          v-for="poll in currentPolls"
          v-bind:key="poll.id"
          v-bind:poll="poll"
        ></pollCard>

        <h6 class="full-width text-center q-ma-xs">Past polls</h6>

        <pollCard
          v-for="poll in pastPolls"
          v-bind:key="poll.id"
          v-bind:poll="poll"
        ></pollCard>
        <cardTitle
          :title="as"
          :slugifiedTitle="as"
          :prefix="as"
        ></cardTitle>
        -->
      </template>
    </div>
  </q-page>
</template>

<style>
</style>

<script>
import { list } from 'src/utils/vuexer';
import ListMixin from 'src/common/mixins/ListMixin';
const servicePrefix = 'Poll';
const { getters, actions } = list(servicePrefix);

import pollCard from 'src/components/PollCard';
// import cardTitle from "src/components/CardTitle";

export default {
  name: 'Polls',
  servicePrefix,
  mixins: [ListMixin],
  // props: [],
  components: {
    pollCard,
    // cardTitle,
  },
  computed: getters,
  methods: actions,
  data() {
    return {
      // polls: [],
      // pastPolls: [],
      // currentPolls: [],
      pagination: {
        sortBy: 'endDate',
        descending: false,
        // page: 1, // page to be displayed
        // rowsPerPage: 3, // maximum displayed rows
        // rowsNumber: 10, // virtualy the max number of rows
      },
      // nextPage: null,
    };
  },
  watch: {
    // polls() {
    //   if (this.polls) {
    //     this.polls.forEach((poll) => {
    //       if (poll.endDate && poll.endDate.length > 0) {
    //         this.currentPolls.push(poll);
    //       } else {
    //         this.pastPolls.push(poll);
    //       }
    //     });
    //   }
    // },
    isLoading(val) {
      if (val) {
        this.$refs.bar.start();
      } else {
        this.$refs.bar.stop();
      }
    },
  },
};
</script>
