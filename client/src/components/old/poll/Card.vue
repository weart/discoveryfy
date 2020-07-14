<template>
  <q-card class="my-card">
    <q-img src="https://cdn.quasar.dev/img/parallax2.jpg">
      <div class="absolute-bottom">
        <div class="text-h6">
          {{ poll.name }}
        </div>
        <div class="text-subtitle2">
          {{ poll.id }}
        </div>
      </div>
    </q-img>
    <q-card-actions
      class="bg-blue"
      v-if="isEditable"
    >
      <q-btn
        @click="dialogVote = true"
        :ripple="{ center: true }"
        color="accent"
      >
        {{ $t('Rate songs') }}
      </q-btn>
      <q-btn
        @click="dialogSuggestSong = true"
        :ripple="{ center: true }"
        color="warning"
      >
        {{ $t('Suggest new song') }}
      </q-btn>
      <q-btn
        @click="dialogEndPoll = true"
        :ripple="{ center: true }"
        color="negative"
      >
        {{ $t('Finish poll') }}
      </q-btn>
    </q-card-actions>

    <pollVoteTracksDialog
      :poll="poll"
      :open-dialog="dialogVote"
      @closeDialog="closeDialog"
    />

    <pollAddTrackDialog
      :poll="poll"
      :open-dialog="dialogSuggestSong"
    />

    <pollEndDialog
      :poll="poll"
      :open-dialog="dialogEndPoll"
    />
  </q-card>
</template>

<style scoped>
.my-card {
  width: 100%;
  max-width: 400px;
}
</style>

<script>
import pollAddTrackDialog from 'src/components/PollAddTrackDialog';
import pollVoteTracksDialog from 'src/components/PollVoteTracksDialog';
import pollEndDialog from 'src/components/PollEndDialog';

export default {
  name: 'Poll',
  components: {
    pollAddTrackDialog,
    pollVoteTracksDialog,
    pollEndDialog,
  },
  props: ['poll'],
  data() {
    return {
      dialogVote: false,
      dialogSuggestSong: false,
      dialogEndPoll: false,
      isEditable: true,
    };
  },
  methods: {
    closeDialog: () => {
      console.log('closing...');
      this.dialogVote = false;
    },
  },
  watch: {
    isEditable() {
      console.log('isEditable', this.poll);
      return !this.poll.endDate || this.poll.endDate.length === 0;
    },
  },

};
</script>
