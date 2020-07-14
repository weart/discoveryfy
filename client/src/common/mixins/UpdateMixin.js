import { error, success } from '../../utils/notify';

export default {
  data() {
    return {
      item: {},
    };
  },

  created() {
    this.retrieve(decodeURIComponent(this.$route.params.id));
  },
  beforeDestroy() {
    this.reset();
  },

  watch: {
    deleted(deleted) {
      if (!deleted) {
        return;
      }
      this.$router.push({ name: `${this.$options.servicePrefix}List` });
    },

    error(message) {
      message && error(message, this.$t('Close'));
    },

    deleteError(message) {
      message && error(message, this.$t('Close'));
    },

    updated(val) {
      success(`${val['@id']} ${this.$t('Updated')}.`, this.$t('Close'));
    },

    retrieved(val) {
      this.item = { ...val };
    },
  },

  methods: {
    del() {
      this.deleteItem(this.retrieved).then(() =>
        this.$router.push({ name: `${this.$options.servicePrefix}List` }),
      );
    },

    reset() {
      this.updateReset();
      this.delReset();
      this.createReset();
    },

    onSendForm() {
      this.$refs.updateForm.$children[0].validate().then(success => {
        if (success) {
          this.update(this.item);
        }
      });
    },

    resetForm() {
      this.item = { ...this.retrieved };
    },
  },
};
