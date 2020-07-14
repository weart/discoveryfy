/**
 * Class taken from:
 * https://github.com/XristMisyris/quasar-starter-frontend/blob/master/src/auth.js
 *
 * Router should be changed to:
 * https://github.com/XristMisyris/quasar-starter-frontend/blob/master/src/router.js
 *
 * And change others calls to also use axios?
 * https://quasar.dev/quasar-cli/ajax-requests
 * And even prefetch can be interesting
 * https://quasar.dev/quasar-cli/cli-documentation/prefetch-feature
 */
import Router from 'src/router';
import { LocalStorage } from 'quasar';
import { axiosInstance } from 'boot/axios';
import { success, error } from 'src/utils/notify';
import SubmissionError from 'src/error/SubmissionError';

const LOGIN_URL = 'login';
const SIGNUP_URL = 'register';
// const USER_URL = 'user';
// const REFRESH_TOKEN = 'refresh-token'

export default {

  user: {
    authenticated: false
  },

  login (creds, redirect) {
    axiosInstance.post(LOGIN_URL, creds)
      .then((response) => {
        debugger;
        LocalStorage.set('id_token', response.data.token)

        this.user.authenticated = true
        axiosInstance.defaults.headers.common['Authorization'] = 'Bearer ' + LocalStorage.getItem('id_token')
        // this.getAuthUser()

        if (redirect) {
          setTimeout(() => Router.replace(redirect), 700)
        }
      })
      .catch((error_msg) => {
        debugger;
        // this.$q.notify({type: 'negative', message: error_msg.response.data.message })
        throw new SubmissionError(error_msg);
        error(error_msg.response.data.message, 'Close');
      })
  },

  logout () {
    LocalStorage.clear()
    this.user.authenticated = false
    Router.replace('/')
    // this.$q.notify({type: 'positive', message: 'You\'ve been logged out successfully.' })
    success('You\'ve been logged out successfully.');
  },

  signup (creds, redirect) {
    axiosInstance.post(SIGNUP_URL, creds)
      .then((response) => {
        LocalStorage.set('id_token', response.data.token)

        this.user.authenticated = true
        axiosInstance.defaults.headers.common['Authorization'] = 'Bearer ' + LocalStorage.getItem('id_token')
        this.getAuthUser()

        if (redirect) {
          setTimeout(() => Router.replace(redirect), 700)
        }
      })
      .catch((error) => {
        // this.$q.notify({type: 'negative', message: error_msg.response.data.message })
        throw new SubmissionError(error_msg);
        error(error_msg.response.data.message, 'Close');
      })
  },

  checkAuth () {
    let jwt = LocalStorage.getItem('id_token');

    if (jwt) {
      this.user.authenticated = true;
      axiosInstance.defaults.headers.common['Authorization'] = 'Bearer ' + LocalStorage.getItem('id_token');
      // this.refreshToken();
    }
    else {
      this.user.authenticated = false;
    }
  },

  // refreshToken () {
  //   var that = this
  //
  //   axiosInstance.post(REFRESH_TOKEN)
  //     .then((response) => {
  //       // Store refreshed token
  //       axiosInstance.defaults.headers.common['Authorization'] = 'Bearer ' + response.data.token
  //       LocalStorage.set('id_token', response.data.token)
  //       this.$q.notify({type: 'positive', message: 'You have successfully logged in.' })
  //       that.getAuthUser()
  //     }, () => {
  //       this.$q.notify({type: 'negative', message: 'Something went wrong with your login!!' })
  //       that.logout()
  //     })
  // },

  // getAuthUser () {
  //   axiosInstance.get(USER_URL)
  //     .then((response) => {
  //       LocalStorage.set('user', response.data)
  //     }, () => {
  //       this.$q.notify({type: 'negative', message: 'Something went wrong!' })
  //     })
  // },

  // showLoading () {
  //   Loading.show({
  //     message: 'You got disconnected for security reasons.\n Reconnecting....'
  //   })
  //   setTimeout(() => {
  //     Loading.hide()
  //   }, 2000)
  // }
}
/*
import axios from 'axios'

export function register ({commit}, form) {
  return axios.post('api/auth/register', form)
    .then(response => {
      commit('login', {token: response.data.token, user: response.data.user})
      setAxiosHeaders(response.data.token)
    })
}

function setAxiosHeaders (token) {
  axios.defaults.headers.common['Authorization'] = 'Bearer ' + token
}
this.$axios.get('/api/backend')
  .then((response) => {
    this.data = response.data
  })
  .catch(() => {
    this.$q.notify({
      color: 'negative',
      position: 'top',
      message: 'Loading failed',
      icon: 'report_problem'
    })
  })
  */
