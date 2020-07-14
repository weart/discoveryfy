<template>
  <q-form class="q-gutter-md">
    <q-input
      v-model="item.name"
      filled
      type="text"
      :label="$t('name')"
      lazy-rules
      :rules="[isInvalid('name')]"
    />

    <q-input
      v-model="item.rating"
      filled
      type="number"
      :label="$t('rating')"
      lazy-rules
      :rules="[val => !!val || $t('Field is required'), isInvalid('rating')]"
    />

    <q-select
      v-model="item.poll"
      filled
      :label="$t('poll')"
      lazy-rules
      :rules="[val => !!val || $t('Field is required'), isInvalid('poll')]"
    />

    <q-select
      v-model="item.track"
      filled
      :label="$t('track')"
      lazy-rules
      :rules="[val => !!val || $t('Field is required'), isInvalid('track')]"
    />
  </q-form>
</template>

<script>
export default {
  props: {
    values: {
      type: Object,
      required: true,
    },

    errors: {
      type: Object,
      default: () => {},
    },

    initialValues: {
      type: Object,
      default: () => {},
    },
  },

  computed: {
    // eslint-disable-next-line
    item() {
      return this.initialValues || this.values;
    },

    violations() {
      return this.errors || {};
    },
  },

  methods: {
    isInvalid(key) {
      return (val) => {
        if (!(val && val.length > 0)) return this.$t('Please type something');
        return Object.keys(this.violations).length === 0 && !this.violations[key];
      };
    },
  },
};
</script>
