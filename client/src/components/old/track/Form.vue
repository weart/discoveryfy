<template>
  <q-form class="q-gutter-md">
    <q-input
      v-model="item.spotify_uri"
      filled
      type="text"
      :label="$t('spotify_uri')"
      lazy-rules
      :rules="[isInvalid('spotify_uri')]"
    />

    <q-input
      v-model="item.youtube_uri"
      filled
      type="text"
      :label="$t('youtube_uri')"
      lazy-rules
      :rules="[isInvalid('youtube_uri')]"
    />

    <q-input
      v-model="item.artist"
      filled
      type="text"
      :label="$t('artist')"
      lazy-rules
      :rules="[val => !!val || $t('Field is required'), isInvalid('artist')]"
    />

    <q-input
      v-model="item.name"
      filled
      type="text"
      :label="$t('name')"
      lazy-rules
      :rules="[val => !!val || $t('Field is required'), isInvalid('name')]"
    />

    <q-select
      v-model="item.poll"
      filled
      :label="$t('poll')"
      lazy-rules
      :rules="[val => !!val || $t('Field is required'), isInvalid('poll')]"
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
