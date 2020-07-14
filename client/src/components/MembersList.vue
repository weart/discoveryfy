<template v-if="organization && organization.memberships.length > 0">
  <q-list
    bordered
    padding
  >
    <!-- eslint-disable vue/valid-v-for -->
    <template v-for="member in sortedMemberships">
      <q-item
        :key="member.id"
        :member="member"
      >
        <q-item-section>
          <q-item-label>{{ member.member.id }}</q-item-label>
          <q-item-label caption>
            {{ member.member.username }}
          </q-item-label>
        </q-item-section>

        <q-item-section
          side
          top
        >
          <q-item-label>{{ formatRol(member.rol) }}</q-item-label>
        </q-item-section>
      </q-item>

      <q-separator spaced />
    </template>
  </q-list>
</template>

<script>
export default {
  name: 'TracksList',
  props: {
    organization: {
      type: Object,
      default: null
    },
  },
  computed: {
  	sortedMemberships () {
      return this.organization.memberships.slice(0).sort((a, b) => a.rol < b.rol ? 1 : 0);
    },
  },
  methods: {
    onSendVote() {
      console.log('vote!');
      // this.$router.push({ name: 'Poll', params: { id: this.poll.id } });
    },
    formatRol(rol) {
      if(rol == 0) {
        return 'Owner';
      } else if (rol == 1) {
        return 'Admin';
      } else if (rol == 2) {
        return 'Member';
      } else if (rol == 3) {
        return 'Invited';
      } else {
        return 'Unknown';
      }
    },
  },
};
</script>
