<template>
  <q-dialog
    :value="show"
    persistent
  >
    <q-card>
      <q-card-section class="row items-center">
        <q-avatar
          icon="warning"
          color="primary"
          text-color="white"
        />
        <span class="q-ml-sm">{{ $t('Are you sure you want to delete this item?') }}</span>
      </q-card-section>

      <q-card-actions align="right">
        <q-btn
          flat
          label="Cancel"
          color="primary"
          v-close-popup
        />
        <q-btn
          flat
          label="Delete"
          color="primary"
          v-close-popup
          @click="handleDelete"
        />
      </q-card-actions>
    </q-card>
  </q-dialog>
</template>

<script>
export default {
  name: 'ConfirmDelete',
  props: {
    show: {
      type: Boolean,
      required: true,
    },
    handleDelete: {
      type: Function,
      required: true,
    },
  },
};
</script>
