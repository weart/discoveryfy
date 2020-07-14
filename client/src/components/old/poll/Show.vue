<template>
  <div>
    <q-ajax-bar
      ref="bar"
      position="top"
      color="accent"
      size="10px"
      skip-hijack
    />
    <q-toolbar class="q-my-md">
      <q-breadcrumbs class="q-mr-sm">
        <q-breadcrumbs-el
          icon="home"
          to="/"
        />
        <q-breadcrumbs-el
          v-for="(breadcrumb, idx) in breadcrumbList"
          :key="idx"
          :label="breadcrumb.label"
          :icon="breadcrumb.icon"
          :to="breadcrumb.to"
        />
        <q-breadcrumbs-el
          v-if="item && item['@id']"
          :label="item['@id']"
        />
      </q-breadcrumbs>
      <q-space />
      <div>
        <q-btn
          :label="$t('Delete')"
          color="primary"
          flat
          class="q-ml-sm"
          @click="deleteItem"
        />
      </div>
    </q-toolbar>

    <div
      v-if="item"
      class="table-responsive"
    >
      <q-markup-table>
        <thead>
          <tr>
            <th>{{ $t('Field') }}</th>
            <th>{{ $t('Value') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>{{ $t('name') }}</td>
            <td>{{ item['name'] }}</td>
          </tr>
          <tr>
            <td>{{ $t('spotify_playlist_uri') }}</td>
            <td>{{ item['spotify_playlist_uri'] }}</td>
          </tr>
          <tr>
            <td>{{ $t('tracks') }}</td>
            <td>{{ item['tracks'] }}</td>
          </tr>
          <tr>
            <td>{{ $t('votes') }}</td>
            <td>{{ item['votes'] }}</td>
          </tr>
          <tr>
            <td>{{ $t('startDate') }}</td>
            <td>{{ item['startDate'] }}</td>
          </tr>
          <tr>
            <td>{{ $t('endDate') }}</td>
            <td>{{ item['endDate'] }}</td>
          </tr>
          <tr>
            <td>{{ $t('spotifyUri') }}</td>
            <td>{{ item['spotifyUri'] }}</td>
          </tr>
        </tbody>
      </q-markup-table>
    </div>
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';

export default {
  computed: mapGetters({
    deleteError: 'poll/del/error',
    error: 'poll/show/error',
    isLoading: 'poll/show/isLoading',
    item: 'poll/show/retrieved',
  }),

  beforeDestroy() {
    this.reset();
  },

  created() {
    this.breadcrumbList = this.$route.meta.breadcrumb;
    this.retrieve(decodeURIComponent(this.$route.params.id));
  },

  watch: {
    isLoading(val) {
      if (val) {
        this.$refs.bar.start();
      } else {
        this.$refs.bar.stop();
      }
    },

    error(message) {
      this.$q.notify({
        message,
        color: 'red',
        icon: 'error',
        closeBtn: this.$t('Close'),
      });
    },

    deleteError(message) {
      this.$q.notify({
        message,
        color: 'red',
        icon: 'error',
        closeBtn: this.$t('Close'),
      });
    },
  },

  methods: {
    ...mapActions({
      del: 'poll/del/del',
      reset: 'poll/show/reset',
      retrieve: 'poll/show/retrieve',
    }),

    deleteItem() {
      if (window.confirm(this.$t('Are you sure you want to delete this item?'))) {
        this.del(this.item).then(() => this.$router.push({ name: 'PollList' }));
      }
    },
  },
};
</script>
