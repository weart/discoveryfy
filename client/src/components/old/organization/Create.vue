<template>
  <div class="q-ma-xl">
    <q-ajax-bar
      ref="bar"
      position="top"
      color="accent"
      size="10px"
      skip-hijack
    />
    <q-toolbar class="q-my-md">
      <!--
      <q-breadcrumbs class="q-mr-sm">
        <q-breadcrumbs-el icon="home" to="/" />
        <q-breadcrumbs-el
          v-for="(breadcrumb, idx) in breadcrumbList"
          :key="idx"
          :label="breadcrumb.label"
          :icon="breadcrumb.icon"
          :to="breadcrumb.to"
        />
      </q-breadcrumbs>
      -->
      <h4>New organization</h4>
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
      </div>
    </q-toolbar>

    <OrganizationForm
      ref="createForm"
      :values="item"
      :errors="violations"
    />
  </div>
</template>

<script>
import { createNamespacedHelpers } from 'vuex';
import OrganizationForm from './Form';
// import { date } from 'quasar';

const { mapGetters, mapActions } = createNamespacedHelpers('organization/create');

export default {
  components: {
    OrganizationForm,
  },

  created() {
    this.breadcrumbList = this.$route.meta.breadcrumb;
  },

  data() {
    return {
      item: {},
      breadcrumbList: [],
    };
  },

  computed: mapGetters(['error', 'isLoading', 'created', 'violations']),

  watch: {
    // eslint-disable-next-line object-shorthand,func-names
    created: function (created) {
      if (!created) {
        return;
      }

      // this.$router.push({ name: 'OrganizationUpdate', params: { id: created['@id'] } });
      this.$router.push('/');
    },

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
  },

  methods: {
    ...mapActions(['create']),

    onSendForm() {
      this.create(this.item);
    },

    resetForm() {
      this.item = {};
    },
  },
};
</script>
