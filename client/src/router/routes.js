const routes = [
  {
    path: '/',
    component: () => import('layouts/MyLayout.vue'),
    // component: () => import('layouts/MyLayoutDouble.vue'), // Lleig
    // component: () => import('layouts/MyLayoutGrouped.vue'),
    // component: () => import('layouts/CardLayout.vue'),
    // component: () => import('layouts/IosLayout.vue'), // Nomes per mobil, pero de color primari
    // component: () => import('layouts/DrawerLayout.vue'),
    // component: () => import('layouts/DrawerToolbarLayout.vue'),
    redirect: { name: 'Polls' },
    children: [
      {
        name: 'Polls',
        path: '/polls/',
        component: () => import('pages/Polls.vue'),
      },
      {
        name: 'Poll',
        path: '/poll/:id',
        component: () => import('pages/Poll.vue'),
        meta: {
          breadcrumb: [
            { label: 'Polls', icon: 'gavel', to: { name: 'Polls' } },
          ],
        },
      },
      /*
      {
        path: '/me',
        redirect: {
          name: 'User'
        },
        props: (route) => ({
          //@ToDo: Get user-id from shomewhere
          id: route.query.q
        })
      },
      */
      {
        name: 'User',
        path: '/user/:id',
        component: () => import('pages/User.vue'),
      },
      {
        name: 'Organizations',
        path: '/orgs/',
        component: () => import('pages/Organizations.vue'),
      },
      {
        name: 'Organization',
        path: '/org/:id',
        component: () => import('pages/Organization.vue'),
        meta: {
          breadcrumb: [
            { label: 'Organizations', icon: 'supervisor_account', to: { name: 'Organizations' } },
          ],
        },
      },
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
