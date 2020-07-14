<template>
  <q-dialog
    v-model="openDialog"
    full-width
    transition-show="slide-up"
    transition-hide="slide-down"
  >
    <q-card>
      <q-bar class="bg-primary text-white">
        <div>{{ $t('Suggest new song in ') }}{{ poll.name }}</div>
        <q-space />
        <q-btn
          dense
          flat
          icon="minimize"
          @click="maximizedToggle = false"
          v-if="maximizedToggle"
        >
          <q-tooltip
            v-if="maximizedToggle"
            content-class="bg-white text-primary"
          >
            Minimize
          </q-tooltip>
        </q-btn>
        <q-btn
          dense
          flat
          icon="crop_square"
          @click="maximizedToggle = true"
          v-if="!maximizedToggle"
        >
          <q-tooltip
            v-if="!maximizedToggle"
            content-class="bg-white text-primary"
          >
            Maximize
          </q-tooltip>
        </q-btn>
        <q-btn
          dense
          flat
          icon="close"
          v-close-popup
        >
          <q-tooltip content-class="bg-white text-primary">
            Close
          </q-tooltip>
        </q-btn>
      </q-bar>

      <q-form class="q-gutter-md">
        <q-card-section class="scroll">
          <q-input
            v-model="trackForm.spotify_uri"
            filled
            type="text"
            :label="$t('spotify_uri')"
            lazy-rules
            :rules="[isInvalid('spotify_uri')]"
          />

          <q-input
            v-model="trackForm.youtube_uri"
            filled
            type="text"
            :label="$t('youtube_uri')"
            lazy-rules
            :rules="[isInvalid('youtube_uri')]"
          />

          <q-input
            v-model="trackForm.artist"
            filled
            type="text"
            :label="$t('artist')"
            lazy-rules
            :rules="[val => !!val || $t('Field is required'), isInvalid('artist')]"
          />

          <q-input
            v-model="trackForm.name"
            filled
            type="text"
            :label="$t('name')"
            lazy-rules
            :rules="[val => !!val || $t('Field is required'), isInvalid('name')]"
          />

          <q-separator />
        </q-card-section>
        <q-card-section class="row justify-around items-start">
          <q-btn
            color="primary"
            @click="downloadSpotifyData"
            v-if="fetched"
          >
            {{ $t('Download Spotify data') }}
          </q-btn>
          <q-spinner-audio
            v-if="!fetched"
            color="primary"
            size="2em"
          />
          <q-btn
            color="primary"
            @click="onSendForm"
          >
            {{ $t('Submit') }}
          </q-btn>
        </q-card-section>
      </q-form>
    </q-card>
  </q-dialog>
</template>

<style>
</style>

<script>
import fetchSimple from 'src/utils/fetch_simple';

export default {
  name: 'PollAddTrackDialog',
  props: ['poll', 'openDialog'],
  data() {
    return {
      maximizedToggle: false,
      fetched: true,
      trackForm: {
        spotify_uri: '',
        youtube_uri: '',
        artist: '',
        name: '',
      },
    };
  },
  methods: {
    isInvalid(key) {
      return (val) => {
        if (!(val && val.length > 0)) return this.$t('Please type something');
        return Object.keys(this.violations).length === 0 && !this.violations[key];
      };
    },
    onSendForm() {
      this.create(this.trackForm);
    },
    downloadSpotifyData() {
      console.log(this.trackForm);
      this.fetched = false;
      fetchSimple(`spotify/track_info/${this.trackForm.spotify_uri}`)
        .then((data) => {
          console.log('spotify_info', data, data.image);
          this.trackForm.artist = data.artist_name;
          this.trackForm.name = data.track_name;
        })
        .finally(() => {
          this.fetched = true;
        });
    },
  },
};
</script>
