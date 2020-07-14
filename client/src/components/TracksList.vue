<template v-if="poll && poll.tracks.length > 0">
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
      <q-separator spaced />

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
            v-model="rating"
            size="3.5em"
            icon="music_note"
          />
        </q-item-section>
      </q-item>
    </template>
    <!--eslint-enable-->
    <!--
    <q-item dark class="items-center justify-center">
      <q-btn :label="$t('Vote!')" color="primary" @click="onSendVote" />
    </q-item>
    -->
  </q-list>
</template>

<script>
export default {
  name: 'TracksList',
  props: {
    poll: {
      type: Object,
      default: null
    },
  },
  data() {
    return {
      rating: 0,
    };
  },
  methods: {
    onSendVote() {
      console.log('vote!');
      // this.$router.push({ name: 'Poll', params: { id: this.poll.id } });
    },
  },
  /*
  computed: {
    imgSrc() {
      // return 'https://cdn.quasar.dev/img/parallax2.jpg';
      return (
        this.poll.length > 0
        && this.poll.spotifyPlaylistImages.length > 0
        && this.poll.spotifyPlaylistImages[0].length > 0
        && this.poll.spotifyPlaylistImages[0].url.length > 0
      )
        ? this.poll.spotifyPlaylistImages[0].url : 'https://cdn.quasar.dev/img/parallax2.jpg';
    },
  },
  */
};
</script>
