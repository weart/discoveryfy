<template>
  <q-form class="q-gutter-md">
    <q-input
      v-model="item.name"
      filled
      type="text"
      :label="$t('poll_name')"
      lazy-rules
      :rules="[isInvalid('name')]"
    />

    <q-input
      v-model="item.spotifyPlaylistUri"
      filled
      type="text"
      :label="$t('spotify_playlist_uri')"
      lazy-rules
      :rules="[isInvalid('spotify_playlist_uri')]"
    />

    <q-checkbox
      v-model="item.restartPoll"
      :label="$t('restart_poll')"
      lazy-rules
      :rules="[isInvalid('restart_poll')]"
    />

    <q-input
      v-model="item.spotifyWinnerPlaylistUri"
      filled
      type="text"
      :label="$t('spotify_winner_playlist_uri')"
      lazy-rules
      :rules="[isInvalid('spotify_winner_playlist_uri')]"
    />

    <q-input
      v-model="item.spotifyHistoricPlaylistUri"
      filled
      type="text"
      :label="$t('spotify_historic_uri')"
      lazy-rules
      :rules="[isInvalid('spotify_historic_uri')]"
    />
    <!--
    <q-select
      v-model="item.tracks"
      filled
      :label="$t('tracks')"
      lazy-rules
      :rules="[isInvalid('tracks')]"
    />

    <q-select
      v-model="item.votes"
      filled
      :label="$t('votes')"
      lazy-rules
      :rules="[isInvalid('votes')]"
    />
    -->
  </q-form>
</template>

<script>
export default {
  props: {
    values: {
      type: Object,
      required: true,
    },

    errors: {
      type: Object,
      default: () => {},
    },

    initialValues: {
      type: Object,
      default: () => {},
    },
  },

  computed: {
    // eslint-disable-next-line
    item() {
      return this.initialValues || this.values;
    },

    violations() {
      return this.errors || {};
    },
  },

  methods: {
    isInvalid(key) {
      return (val) => {
        if (!(val && val.length > 0)) return this.$t('Please type something');
        return Object.keys(this.violations).length === 0 && !this.violations[key];
      };
    },
  },
};
</script>
