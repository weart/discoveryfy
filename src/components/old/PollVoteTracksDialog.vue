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
        <div>{{ $t('Rate the songs in ') }} {{ poll.name }}</div>
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

      <q-card-section
        style="max-height: 50vh"
        class="scroll"
      >
        <template v-if="poll.tracks.length == 0">
          <h5>Without songs</h5>
        </template>

        <template v-if="poll.tracks.length > 0">
          <q-list
            bordered
            padding
          >
            <q-item-label
              header
              class="text-center text-h6"
            >
              {{ $t('You can give 10 points between all songs') }}
            </q-item-label>

            <!-- eslint-disable vue/valid-v-for -->
            <template v-for="track in poll.tracks">
              <q-item
                :key="track.id"
                :track="track"
              >
                <q-item-section>
                  <q-item-label>{{ track.artist }}</q-item-label>
                  <q-item-label
                    caption
                    lines="2"
                  >
                    {{ track.name }}
                  </q-item-label>
                </q-item-section>

                <q-item-section
                  side
                  top
                >
                  <q-rating
                    v-model="track.rating"
                    size="3.5em"
                    icon="music_note"
                  />
                </q-item-section>
              </q-item>

              <q-separator spaced />
            </template>
            <!--eslint-enable-->

            <q-item
              dark
              class="items-center justify-center"
            >
              <q-btn
                :label="$t('Vote!')"
                color="primary"
                @click="onSendVote"
              />
            </q-item>
          </q-list>
        </template>

        <q-spinner-audio
          v-if="!fetched"
          color="primary"
          size="2em"
        />
      </q-card-section>
    </q-card>
  </q-dialog>
</template>

<style>
</style>

<script>
import fetchSimple from 'src/utils/fetch_simple';

export default {
  name: 'PollVoteTrackDialog',
  props: ['poll', 'openDialog'],
  data() {
    return {
      maximizedToggle: false,
      fetched: false,
      ratingModel: 0,
    };
  },
  computed: {
    isShown() {
      return this.openDialog;
    },
  },
  methods: {
    onSendVote() {
      console.log('sendVote');
      // this.create(this.trackForm);
    },
  },
  watch: {
    isShown(newVal) {
      if (newVal && !this.fetched) {
        fetchSimple(`polls/${this.poll.id}/tracks`).then((data) => {
          data.forEach((track) => {
            if (!Object.prototype.hasOwnProperty.call(track, 'rating')) {
              track.rating = 0;
            }
          });
          // for (let track in data) {
          //     if (data.hasOwnProperty(track) && !track.hasOwnProperty('rating')) {
          //         track.rating = 0;
          //     }
          // }
          console.log('tracks', data);
          this.poll.tracks = data;
          this.fetched = true;
        });
      }
    },
    hide() {
      console.log('closeDialog');
      this.$emit('closeDialog');
    },
  },
};
</script>
