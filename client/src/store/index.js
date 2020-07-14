import Vue from 'vue';
import Vuex from 'vuex';

import membership from 'src/store/modules/membership/';
import organization from 'src/store/modules/organization/';
import poll from 'src/store/modules/poll/';
import session from 'src/store/modules/session/';
import track from 'src/store/modules/track/';
import user from 'src/store/modules/user/';
import vote from 'src/store/modules/vote/';

Vue.use(Vuex);
/*
export const store = new Vuex.Store({
  // ...
  modules: {
    membership,
    organization,
    poll,
    session,
    track,
    user,
    vote
  }
});
*/

/*
 * If not building with SSR mode, you can
 * directly export the Store instantiation
 */

export default function (/* { ssrContext } */) {
  const Store = new Vuex.Store({
    modules: {
      membership,
      organization,
      poll,
      session,
      track,
      user,
      vote,
    },
    // enable strict mode (adds overhead!)
    // for dev mode only
    // strict: process.env.DEV,
    strict: false,
    devtools: true,
  });

  return Store;
};
