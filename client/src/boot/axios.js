import Vue from 'vue'
import axios from 'axios'
import { ENTRYPOINT } from 'src/config/entrypoint';

// We create our own axios instance and set a custom base URL.
// Note that if we wouldn't set any config here we do not need
// a named export, as we could just `import axios from 'axios'`
const axiosInstance = axios.create({
  baseURL: ENTRYPOINT,
});

// we add it to Vue prototype
// so we can reference it in Vue files
// without the need to import axios
Vue.prototype.$axios = axiosInstance;

// Example: this.$axios will reference Axios now so you don't need stuff like vue-axios

// Here we define a named export
// that we can later use inside .js files like this: import { axiosInstance } from 'boot/axios'
export { axiosInstance };

/**https://github.com/XristMisyris/quasar-starter-frontend/blob/master/src/main.js
// Loading indicator for ajax requests + refresh token if token is expired
axios.interceptors.request.use(function (config) {
  Loading.show()
  return config
}, function (error) {
  Loading.hide()
  return Promise.reject(error)
})

axios.interceptors.response.use(function (response) {
  Loading.hide()
  // Refresh Token if token is expired
  if (response.status === 401 && response.body.error === 'token_expired') {
    auth.refreshToken()
    auth.showLoading()
  }
  return response
}, function (error) {
  Loading.hide()
  return Promise.reject(error)
})
*/
