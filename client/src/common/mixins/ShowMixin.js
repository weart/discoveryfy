import { extractDate } from '../../utils/dates';

export default {
  created() {
    // this.retrieve(decodeURIComponent(this.$route.params.id));
    // Modified by Lenin
    const lowmod = this.$options.servicePrefix.toLowerCase();
    this.retrieve(`/${lowmod}s/${decodeURIComponent(this.$route.params.id)}`);
    console.log('created retrieve', lowmod);
  },
  beforeDestroy() {
    this.reset();
  },
  watch: {
    error(message) {
      message &&
        this.$q.notify({
          message,
          color: 'red',
          icon: 'error',
          closeBtn: this.$t('Close'),
        });
    },

    deleteError(message) {
      message &&
        this.$q.notify({
          message,
          color: 'red',
          icon: 'error',
          closeBtn: this.$t('Close'),
        });
    },
  },

  methods: {
    formatDateTime(val, format) {
      return val ? this.$d(extractDate(val), format) : '';
    },

    deleteItem() {
      this.deleteItem(this.item).then(() => this.$router.push({ name: `${this.$options.servicePrefix}List` }));
    },
  },
};
