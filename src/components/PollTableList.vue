<template>
  <div>
    <q-table
      :data="items"
      :columns="columns"
      :pagination.sync="pagination"
      @request="onRequest"
      row-key="id"
      :no-data-label="$t('Data unavailable')"
      :no-results-label="$t('No results')"
      :loading-label="$t('Loading...')"
      :rows-per-page-label="$t('Records per page:')"
      flat
      :loading="isLoading"
    >
      <ActionCell
        slot="body-cell-action"
        slot-scope="props"
        :handle-show="() => showHandler(props.row)"
        :handle-edit="() => editHandler(props.row)"
        :handle-delete="() => deleteHandler(props.row)"
      />
    </q-table>
  </div>
</template>

<script>
    import { list } from 'src/utils/vuexer';
    import ActionCell from 'src/common/components/ActionCell';

    import ListMixin from 'src/common/mixins/ListMixin';
    const servicePrefix = 'Poll';

    const { getters, actions } = list(servicePrefix);

    export default {
        name: 'PollList',
        servicePrefix,
        mixins: [ListMixin],
        components: {
            ActionCell,
        },

        data() {
            return {
                columns: [
                    { name: 'action' },
                    { name: 'id', field: '@id', label: this.$t('id') },
                    { name: 'name', field: 'name', label: this.$t('name') },
                    {
                        name: 'startDate',
                        field: 'startDate',
                        label: this.$t('startDate'),
                        format: val => this.formatDateTime(val, 'long'),
                    },
                    {
                        name: 'endDate',
                        field: 'endDate',
                        label: this.$t('endDate'),
                        format: val => this.formatDateTime(val, 'long'),
                    },
                    { name: 'restartDate', field: 'restartDate', label: this.$t('restartDate') },
                    { name: 'spotifyPlaylistImages', field: 'spotifyPlaylistImages', label: this.$t('spotifyPlaylistImages') },
                    { name: 'spotifyPlaylistUri', field: 'spotifyPlaylistUri', label: this.$t('spotifyPlaylistUri') },
                    { name: 'spotifyPlaylistUriRaw', field: 'spotifyPlaylistUriRaw', label: this.$t('spotifyPlaylistUriRaw') },
                    { name: 'spotifyWinnerPlaylistUri', field: 'spotifyWinnerPlaylistUri', label: this.$t('spotifyWinnerPlaylistUri') },
                    { name: 'spotifyWinnerPlaylistUriRaw', field: 'spotifyWinnerPlaylistUriRaw', label: this.$t('spotifyWinnerPlaylistUriRaw') },
                    { name: 'spotifyHistoricPlaylistUri', field: 'spotifyHistoricPlaylistUri', label: this.$t('spotifyHistoricPlaylistUri') },
                    { name: 'spotifyHistoricPlaylistUriRaw', field: 'spotifyHistoricPlaylistUriRaw', label: this.$t('spotifyHistoricPlaylistUriRaw') },
                    { name: 'publicVisibility', field: 'publicVisibility', label: this.$t('publicVisibility') },
                    { name: 'anonCanVote', field: 'anonCanVote', label: this.$t('anonCanVote') },
                    { name: 'anonCanAddTrack', field: 'anonCanAddTrack', label: this.$t('anonCanAddTrack') },
                    {
                        name: 'anonVotesMaxRating',
                        field: 'anonVotesMaxRating',
                        label: this.$t('anonVotesMaxRating'),
                        format: val => this.$n(val),
                    },
                    {
                        name: 'userVotesMaxRating',
                        field: 'userVotesMaxRating',
                        label: this.$t('userVotesMaxRating'),
                        format: val => this.$n(val),
                    },
                    { name: 'multipleUserTracks', field: 'multipleUserTracks', label: this.$t('multipleUserTracks') },
                    { name: 'multipleAnonTracks', field: 'multipleAnonTracks', label: this.$t('multipleAnonTracks') },
                    { name: 'organization', field: 'organization', label: this.$t('organization') },
                    { name: 'tracks', field: 'tracks', label: this.$t('tracks') },
                    { name: 'tracksRaw', field: 'tracksRaw', label: this.$t('tracksRaw') },
                    { name: 'trackOrderByVoted', field: 'trackOrderByVoted', label: this.$t('trackOrderByVoted') },
                    { name: 'votes', field: 'votes', label: this.$t('votes') },
                    { name: 'votesRaw', field: 'votesRaw', label: this.$t('votesRaw') },
                ],
            };
        },

        computed: getters,
        methods: actions,
    };
</script>
