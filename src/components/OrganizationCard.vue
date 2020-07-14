<template>
  <q-card
    dark
    bordered
    class="bg-grey-9"
    @click="goToOrg"
  >
    <q-card-section>
      <div class="text-h6">
        {{ organization.name }}
      </div>
    </q-card-section>

    <q-separator
      dark
      inset
    />

    <q-card-section>id: {{ organization.id }}</q-card-section>
    <q-card-section>Created at: {{ organization.createdAt }}</q-card-section>
    <q-card-section>Num members: {{ organization.numMemberships }}</q-card-section>
    <q-card-section>Num polls: {{ organization.numPolls }}</q-card-section>
    <q-card-section>Public visibility: {{ organization.publicVisibility }}</q-card-section>
    <q-card-section>Public membership: {{ organization.publicMembership }}</q-card-section>
  </q-card>
</template>

<style>
</style>

<script>
export default {
  name: 'Organization',
  props: {
    organization: {
      type: Object,
      default: null
    },
  },
  methods: {
    goToOrg() {
      this.$router.push({ name: 'Organization', params: { id: this.organization.id } });
    },
  },
};
</script>
