
// import pollRoutes from './router/poll'

const routes = [
  {
    path: '/',
    component: () => import('layouts/MyLayout.vue'),
    children: [
      { path: '', component: () => import('pages/Index.vue') },
    ],
  },
];

// Always leave this as last one
if (process.env.MODE !== 'ssr') {
  routes.push({
    path: '*',
    component: () => import('pages/Error404.vue'),
  });
}

export default routes;


/*

//import routes
import pollRoutes from './router/poll';

// Add routes to VueRouter
const router = new VueRouter({
  // ...
  routes: [
    ...pollRoutes,
  ]
});

// Add the modules in the store
import poll from './store/modules/poll/';

export const store = new Vuex.Store({
  // ...
  modules: {
    poll
  }
});

Code for the "Track" resource type has been generated!
  Paste the following definitions in your application configuration:


//import routes
import trackRoutes from './router/track';

// Add routes to VueRouter
const router = new VueRouter({
  // ...
  routes: [
    ...trackRoutes,
  ]
});

// Add the modules in the store
import track from './store/modules/track/';

export const store = new Vuex.Store({
  // ...
  modules: {
    track
  }
});

Code for the "Vote" resource type has been generated!
  Paste the following definitions in your application configuration:

//import routes
import voteRoutes from './router/vote';

// Add routes to VueRouter
const router = new VueRouter({
  // ...
  routes: [
    ...voteRoutes,
  ]
});

// Add the modules in the store
import vote from './store/modules/vote/';

export const store = new Vuex.Store({
  // ...
  modules: {
    vote
  }
});
*/
