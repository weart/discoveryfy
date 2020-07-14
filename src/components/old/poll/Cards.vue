<template>
  <div class="row q-pa-md q-gutter-md justify-around items-start">
    <h6 class="full-width text-center q-ma-xs">
      Current polls
    </h6>

    <card
      v-for="poll in currentPolls"
      :key="poll.id"
      :poll="poll"
    />

    <h6 class="full-width text-center q-ma-xs">
      Past polls
    </h6>

    <card
      v-for="poll in pastPolls"
      :key="poll.id"
      :poll="poll"
    />

    <q-spinner-audio
      v-if="isLoading"
      color="primary"
      size="2em"
    />
  </div>
</template>

<script>
import fetchSimple from 'src/utils/fetch_simple';
import card from './Card';

export default {
  name: 'Polls',
  props: ['organization'],
  components: {
    card,
  },
  data() {
    return {
      isLoading: true,
      polls: [],
      pastPolls: [],
      currentPolls: [],
    };
  },
  created() {
    // const endpoint
    // const entryPoint = ENTRYPOINT + (ENTRYPOINT.endsWith('/') ? '' : '/');
    // let params = {};
    // if (this.organization) {
    //     params.set()
    // }
    //   /organizations/{id}/polls
    fetchSimple('polls').then((data) => {
      this.polls = data;
      this.isLoading = false;
    });
  },
  watch: {
    polls() {
      if (this.polls) {
        this.polls.forEach((poll) => {
          if (poll.endDate && poll.endDate.length > 0) {
            this.currentPolls.push(poll);
          } else {
            this.pastPolls.push(poll);
          }
        });
      }
    },
  },
};
/*
    import axios from 'axios'
    import {
        QToolbar,
        QToolbarTitle,
        QList,
        QItem,
        QItemMain,
        QItemTile,
        QSpinnerCircles,
    } from 'quasar-framework/dist/quasar.mat.esm'

    export default {
        name: 'Movies',
        components: {
            QToolbar,
            QToolbarTitle,
            QList,
            QItem,
            QItemMain,
            QItemTile,
            QSpinnerCircles,
        },
        data() {
            return {
                msg: 'List of Ghibli movies',
                isLoading: true,
                movies: [],
                id: undefined,
            }
        },
        created() {
            const uri = 'https://ghibliapi.herokuapp.com/films'
            axios.get(uri)
                .then(res => {
                    this.movies = res.data
                })
                .catch((err) => {
                    console.warn('error during http call', err)
                })
                .finally(() => this.isLoading = false)
        },
        methods: {
            showMovie(ev) {
                console.log(ev, 'todo route to the movie page')
            },
        },
    }
    */
</script>
