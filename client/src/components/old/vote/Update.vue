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
          :label="$t('Submit')"
          color="primary"
          @click="onSendForm"
        />
        <q-btn
          :label="$t('Reset')"
          color="primary"
          flat
          class="q-ml-sm"
          @click="resetForm"
        />
        <q-btn
          :label="$t('Delete')"
          color="primary"
          flat
          class="q-ml-sm"
          @click="del"
        />
      </div>
    </q-toolbar>

    <VoteForm
      v-if="item"
      :values="item"
      :errors="violations"
    />
  </div>
</template>

<script>
import { mapActions, mapGetters } from 'vuex';
import VoteForm from './Form.vue';

export default {
  components: {
    VoteForm,
  },

  data() {
    return {
      item: {},
    };
  },

  computed: {
    ...mapGetters({
      isLoading: 'vote/update/isLoading',
      error: 'vote/update/error',
      deleteError: 'vote/del/error',
      deleteLoading: 'vote/del/isLoading',
      deleted: 'vote/del/deleted',
      retrieved: 'vote/update/retrieved',
      updated: 'vote/update/updated',
      violations: 'vote/update/violations',
    }),
  },

  watch: {
    // eslint-disable-next-line object-shorthand,func-names
    deleted: function (deleted) {
      if (!deleted) {
        return;
      }

      this.$router.push({ name: 'VoteList' });
    },

    isLoading(val) {
      if (val) {
        this.$refs.bar.start();
      } else {
        this.$refs.bar.stop();
      }
    },

    deleteLoading(val) {
      if (val) {
        this.$refs.bar.start();
      } else {
        this.$refs.bar.stop();
      }
    },

    error(message) {
      message
        && this.$q.notify({
          message,
          color: 'red',
          icon: 'error',
          closeBtn: this.$t('Close'),
        });
    },

    deleteError(message) {
      message
        && this.$q.notify({
          message,
          color: 'red',
          icon: 'error',
          closeBtn: this.$t('Close'),
        });
    },

    updated(val) {
      this.$q.notify({
        message: `${val['@id']} ${this.$t('updated')}.`,
        color: 'green',
        icon: 'tag_faces',
        closeBtn: this.$t('Close'),
      });
    },

    retrieved(val) {
      this.item = { ...val };
    },
  },

  beforeDestroy() {
    this.reset();
  },

  created() {
    this.breadcrumbList = this.$route.meta.breadcrumb;
    this.retrieve(decodeURIComponent(this.$route.params.id));
  },

  methods: {
    ...mapActions({
      createReset: 'vote/create/reset',
      deleteItem: 'vote/del/del',
      delReset: 'vote/del/reset',
      retrieve: 'vote/update/retrieve',
      updateReset: 'vote/update/reset',
      update: 'vote/update/update',
    }),

    del() {
      if (window.confirm(this.$t('Are you sure you want to delete this vote ?'))) {
        this.deleteItem(this.retrieved);
      }
    },

    reset() {
      this.updateReset();
      this.delReset();
      this.createReset();
    },

    onSendForm() {
      this.update(this.item);
    },

    resetForm() {
      this.item = { ...this.retrieved };
    },
  },
};
</script>
