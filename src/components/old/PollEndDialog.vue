<template>
  <q-dialog
    v-model="openDialog"
    full-width
    :maximized="maximizedToggle"
    transition-show="slide-up"
    transition-hide="slide-down"
  >
    <q-card>
      <q-bar class="bg-primary text-white">
        <div>{{ $t('Finish the current poll ') }} {{ poll.name }}</div>
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
            v-model="endForm.winner_spotify_uri"
            filled
            type="text"
            :label="$t('Save winner into the playlist')"
            lazy-rules
            :rules="[isInvalid('winner_spotify_uri')]"
          />

          <q-input
            v-model="endForm.history_spotify_uri"
            filled
            type="text"
            :label="$t('Save all songs into the playlist')"
            lazy-rules
            :rules="[isInvalid('history_spotify_uri')]"
          />

          <q-checkbox
            v-model="endForm.empty_spotify_playlist"
            :label="$t('Remove all songs from the playlist')"
          />

          <q-checkbox
            v-model="endForm.finish_poll"
            :label="$t('Finish the poll')"
          />

          <q-separator />
        </q-card-section>

        <q-card-section class="row justify-around items-start">
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
// import fetchSimple from 'src/utils/fetch_simple';

export default {
  name: 'PollEndDialog',
  props: ['poll', 'openDialog'],
  data() {
    return {
      maximizedToggle: false,
      endForm: {
        winner_spotify_uri: '',
        history_spotify_uri: '',
        empty_spotify_playlist: false,
        finish_poll: false,
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
  },
  hide() {
    console.log('closeDialog');
    this.$emit('closeDialog');
  },
};
</script>
