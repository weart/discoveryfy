<template>
  <q-page>
    <!--<q-ajax-bar ref="bar" position="top" color="accent" size="10px" skip-hijack />-->

    <q-toolbar class="q-my-md">
      <q-breadcrumbs class="q-mr-sm">
        <q-breadcrumbs-el
          v-for="(breadcrumb, idx) in $route.meta.breadcrumb"
          :key="idx"
          :label="breadcrumb.label"
          :icon="breadcrumb.icon"
          :to="breadcrumb.to"
        />
        <q-breadcrumbs-el
          v-if="item"
          :label="item.name"
        />
      </q-breadcrumbs>
      <q-space />
      <q-btn
        flat
        round
        dense
        icon="add"
        @click="pollCreate"
      />
    </q-toolbar>

    <div
      class="row q-pa-md q-gutter-md justify-around items-start"
      v-if="isLoading"
    >
      <q-spinner-audio
        v-if="isLoading"
        color="primary"
        size="2em"
      />
    </div>

    <template v-if="!isLoading && item">
      <q-card
        flat
        bordered
        class="center-card"
      >
        <q-toolbar>
          <q-card-section class="card-title">
            <div>Tracks</div>
          </q-card-section>
          <q-space />
          <q-btn
            flat
            round
            icon="library_music"
          >
            <q-tooltip>Go to Spotify playlist</q-tooltip>
          </q-btn>
          <q-btn
            flat
            rounded
            icon="add"
            label="Suggest new song"
          >
          <!--<q-tooltip>Suggest new song</q-tooltip>-->
          </q-btn>
          <q-btn
            flat
            rounded
            icon="how_to_vote"
            label="Vote!"
            :loading="voteLoading"
            @click="voteCreate"
          >
            <template v-slot:loading>
              <q-spinner-gears />
            </template>
          </q-btn>
        </q-toolbar>

        <q-separator />

        <q-card-section
          style="max-height: 50vh"
          class="scroll"
        >
          <template v-if="!item || !item.tracks || item.tracks.length == 0">
            <h5>Without songs</h5>
          </template>
          <template v-if="item && item.tracks.length > 0">
            <tracks-list :poll="item" />
          </template>
        </q-card-section>
      </q-card>

      <br><br>

      <q-card
        flat
        bordered
        class="center-card"
      >
        <q-toolbar>
          <q-card-section class="card-title">
            <div>Properties</div>
          </q-card-section>
          <q-space />
          <q-btn
            flat
            rounded
            icon="delete_forever"
            color="warning"
            label="Delete poll"
          >
            <q-tooltip>Delete poll</q-tooltip>
          </q-btn>
          <q-btn
            flat
            rounded
            icon="save"
            label="Save changes"
          >
            <q-tooltip>Save changes</q-tooltip>
          </q-btn>
        </q-toolbar>

        <q-separator />

        <q-card-section
          style="max-height: 50vh"
          class="scroll"
        >
          <q-form>
            <q-tabs
              v-model="tab"
              dense
              class="text-grey"
              active-color="primary"
              indicator-color="primary"
              align="justify"
              narrow-indicator
            >
              <q-tab
                name="basic"
                label="Basic"
              />
              <q-tab
                name="spotify"
                label="Spotify"
              />
              <q-tab
                name="advanced"
                label="Advanced"
              />
            </q-tabs>

            <q-separator />

            <q-tab-panels
              v-model="tab"
              animated
            >
              <q-tab-panel name="basic">
                <q-toggle
                  label="Readonly"
                  v-model="readonly"
                  color="primary"
                  keep-color
                />
                <q-toggle
                  label="Disable"
                  v-model="disable"
                  color="primary"
                  keep-color
                />
                <q-input
                  label="id"
                  v-model="item.id"
                  readonly
                  disable
                />
                <q-input
                  label="Organization"
                  v-model="item.organization.name"
                  readonly
                  disable
                />
                <q-input
                  label="name"
                  v-model="item.name"
                  :readonly="readonly"
                  :disable="disable"
                />

                <!-- timming -->
                <q-input
                  label="startDate"
                  v-model="item.startDate"
                  :readonly="readonly"
                  :disable="disable"
                >
                  <template v-slot:prepend>
                    <q-icon
                      name="event"
                      class="cursor-pointer"
                    >
                      <q-popup-proxy
                        transition-show="scale"
                        transition-hide="scale"
                      >
                        <q-date
                          v-model="item.startDate"
                          mask="YYYY-MM-DDTHH:mm:ssZ"
                          minimal
                        />
                      </q-popup-proxy>
                    </q-icon>
                  </template>

                  <template v-slot:append>
                    <q-icon
                      name="access_time"
                      class="cursor-pointer"
                    >
                      <q-popup-proxy
                        transition-show="scale"
                        transition-hide="scale"
                      >
                        <q-time
                          v-model="item.startDate"
                          mask="YYYY-MM-DDTHH:mm:ssZ"
                          format24h
                        />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>
                <q-input
                  label="endDate"
                  v-model="item.endDate"
                  :readonly="readonly"
                  :disable="disable"
                >
                  <template v-slot:prepend>
                    <q-icon
                      name="event"
                      class="cursor-pointer"
                    >
                      <q-popup-proxy
                        transition-show="scale"
                        transition-hide="scale"
                      >
                        <q-date
                          v-model="item.endDate"
                          mask="YYYY-MM-DDTHH:mm:ssZ"
                          minimal
                        />
                      </q-popup-proxy>
                    </q-icon>
                  </template>

                  <template v-slot:append>
                    <q-icon
                      name="access_time"
                      class="cursor-pointer"
                    >
                      <q-popup-proxy
                        transition-show="scale"
                        transition-hide="scale"
                      >
                        <q-time
                          v-model="item.endDate"
                          mask="YYYY-MM-DDTHH:mm:ssZ"
                          format24h
                        />
                      </q-popup-proxy>
                    </q-icon>
                  </template>
                </q-input>

                <q-input
                  label="restartDate"
                  v-model="item.restartDate"
                  :readonly="readonly"
                  :disable="disable"
                />
              </q-tab-panel>

              <q-tab-panel name="spotify">
                <q-list>
                  <q-item>
                    <q-item-section>
                      <!--
                      <q-input label="spotifyPlaylistImages"
                        v-model="item.spotifyPlaylistImages" :readonly="readonly" :disable="disable" />
                      -->
                      <q-input
                        label="spotifyPlaylistUri"
                        v-model="item.spotifyPlaylistUri"
                        :readonly="readonly"
                        :disable="disable"
                      />
                    </q-item-section>
                  </q-item>
                  <q-item>
                    <q-item-section>
                      <q-input
                        label="spotifyWinnerPlaylistUri"
                        v-model="item.spotifyWinnerPlaylistUri"
                        :readonly="readonly"
                        :disable="disable"
                      />
                    </q-item-section>
                  </q-item>
                  <q-item>
                    <q-item-section>
                      <q-input
                        label="spotifyHistoricPlaylistUri"
                        v-model="item.spotifyHistoricPlaylistUri"
                        :readonly="readonly"
                        :disable="disable"
                      />
                    </q-item-section>
                  </q-item>
                  <q-item>
                    <q-item-section avatar>
                      <q-item-label>
                        Make this playlist public in Spotify?
                      </q-item-label>
                      <q-item-label caption>
                        You can only set public to true on non-collaborative playlists
                      </q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-btn-toggle
                        v-model="item.spotifyPlaylistPublic"
                        spread
                        rounded
                        no-caps
                        :readonly="readonly"
                        :disable="disable"
                        :options="[
                          {label: 'Yes', value: true},
                          {label: 'No', value: false},
                        ]"
                      />
                    </q-item-section>
                  </q-item>
                  <q-item>
                    <q-item-section avatar>
                      <q-item-label>
                        Make this playlist collaborative in Spotify?
                      </q-item-label>
                      <q-item-label caption>
                        Other users will be able to modify the playlist in their Spotify client
                      </q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-btn-toggle
                        v-model="item.spotifyPlaylistCollaborative"
                        spread
                        rounded
                        no-caps
                        :readonly="readonly"
                        :disable="disable"
                        :options="[
                          {label: 'Yes', value: true},
                          {label: 'No', value: false},
                        ]"
                      />
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-tab-panel>


              <q-tab-panel name="advanced">
                <q-list>
                  <q-item>
                    <q-item-section>
                      <q-btn-toggle
                        v-model="item.publicVisibility"
                        spread
                        rounded
                        no-caps
                        :readonly="readonly"
                        :disable="disable"
                        :options="[
                          {label: 'Visible to everyone', value: true},
                          {label: 'Visible only to members of the group', value: false},
                        ]"
                      />
                    </q-item-section>
                  </q-item>

                  <q-item>
                    <q-item-section>
                      <q-btn-toggle
                        v-model="item.anonCanVote"
                        spread
                        rounded
                        no-caps
                        :readonly="readonly"
                        :disable="disable"
                        :options="[
                          {label: 'Anyone can vote', value: true},
                          {label: 'Only members of the group can vote', value: false},
                        ]"
                      />
                    </q-item-section>
                  </q-item>

                  <q-item>
                    <q-item-section>
                      <q-btn-toggle
                        v-model="item.whoCanAddTrack"
                        spread
                        rounded
                        no-caps
                        :readonly="readonly"
                        :disable="disable"
                        :options="[
                          {label: 'Anyone can suggest new tracks', value: 4},
                          {label: 'Only members of the group can suggest new tracks', value: 2},
                          {label: 'Only admin of the group can suggest new tracks', value: 1},
                          {label: 'Only owner of the group can suggest new tracks', value: 0},
                          {label: 'Nobody can suggest new tracks', value: null},
                        ]"
                      />
                    </q-item-section>
                  </q-item>

                  <q-item>
                    <q-item-section>
                      <q-btn-toggle
                        v-model="item.multipleUserTracks"
                        spread
                        rounded
                        no-caps
                        :readonly="readonly"
                        :disable="disable"
                        :options="[
                          {label: 'Members of the group can suggest more than one track', value: true},
                          {label: 'Members of the group can suggest only one track', value: false},
                        ]"
                      />
                    </q-item-section>
                  </q-item>

                  <q-item>
                    <q-item-section>
                      <q-btn-toggle
                        v-model="item.multipleAnonTracks"
                        spread
                        rounded
                        no-caps
                        :readonly="readonly"
                        :disable="disable"
                        :options="[
                          {label: 'Anyone can suggest more than one track', value: true},
                          {label: 'Anyone can suggest only one track', value: false},
                        ]"
                      />
                    </q-item-section>
                  </q-item>

                  <q-item>
                    <q-item-section avatar>
                      <q-item-label>
                        How many points can give anyone in one vote? (Between 0 and 100)
                      </q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-slider
                        v-model="item.anonVotesMaxRating"
                        :min="0"
                        :max="100"
                        label
                        :readonly="readonly"
                        :disable="disable"
                      />
                    </q-item-section>
                  </q-item>

                  <q-item>
                    <q-item-section avatar>
                      <q-item-label>
                        How many point can give members of the group in one vote? (Between 1 and 100)
                      </q-item-label>
                    </q-item-section>
                    <q-item-section>
                      <q-slider
                        v-model="item.userVotesMaxRating"
                        :min="1"
                        :max="100"
                        label
                        :readonly="readonly"
                        :disable="disable"
                      />
                    </q-item-section>
                  </q-item>
                </q-list>
              </q-tab-panel>
            </q-tab-panels>
          </q-form>
        </q-card-section>
      </q-card>
    </template>
  </q-page>
</template>

<style>

</style>

<script>
import { show } from 'src/utils/vuexer';
import ShowMixin from 'src/common/mixins/ShowMixin';
const servicePrefix = 'Poll';
const { getters, actions } = show(servicePrefix);
import tracksList from 'src/components/TracksList';

export default {
  name: 'Poll',
  servicePrefix,
  mixins: [ShowMixin],
  components: {
    tracksList,
  },
  data() {
    return {
      voteLoading: false,
      voteValid: false,
      readonly: false,
      disable: false,
      tab: 'basic',
    };
  },
  computed: {
    ...getters
  },
  methods: {
    ...actions,
    pollCreate() {
      console.log('@todo pollCreate');
      // put /polls & go
      // this.$router.push({ name: 'Poll', params: { id: this.poll.id } });
    },
    voteCreate() {
      // we set loading state
      this.voteLoading = true;
      // simulate a delay
      setTimeout(() => {
        // we're done, we reset loading state
        this.voteLoading = false;
        if (this.voteValid !== true) {
          this.$q.notify({
            message: 'Problems...',
            color: 'warning',
            icon: 'warning',
            timeout: 3000,
            position: 'bottom',
          });
        } else {
          this.$q.notify({
            message: 'Saved',
            color: 'primary',
            icon: 'cloud_done',
            timeout: 3000,
            position: 'bottom',
          });
        }
      }, 3000);
    },

  },

  // watch: {
  //   isLoading(val) {
  //     if (val) {
  //       this.$refs.bar.start();
  //     } else {
  //       this.$refs.bar.stop();
  //     }
  //   },
  // },
};

</script>
