<template>
  <q-card
    class="my-card"
    @click="goToPolls"
  >
    <q-img src="https://cdn.quasar.dev/img/parallax2.jpg">
      <div class="absolute-bottom">
        <div class="text-h6">
          {{ organization.name }}
        </div>
        <div class="text-subtitle2">
          {{ organization.id }}
        </div>
      </div>
    </q-img>

    <q-card-actions
      class="bg-blue"
      v-if="isEditable"
    >
      <q-btn
        push
        color="accent"
        label="Edit"
        :ripple="{ center: true }"
        :to="{ name: 'OrganizationUpdate', params: { organization: organization } }"
      />
      <q-btn
        push
        color="negative"
        label="Remove"
        :ripple="{ center: true }"
        :to="{ name: 'OrganizationDelete', params: { organization: organization } }"
      />
    </q-card-actions>
  </q-card>
</template>

<style scoped>
.my-card {
  width: 100%;
  max-width: 400px;
}
</style>

<script>
export default {
  name: 'Organization',
  props: ['organization'],
  data() {
    return {
      isEditable: false,
    };
  },
  methods: {
    goToPolls() {
      this.$router.push({ name: 'OrganizationPolls', params: { id: this.organization.id } });
    },
  },
};
</script>
