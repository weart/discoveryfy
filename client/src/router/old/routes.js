import pollRoutes from 'src/router/poll';
import organizationRoutes from 'src/router/organization';

const routes = [
  {
    path: '/',
    component: () => import('layouts/MyLayout.vue'),
    children: [
      {
        path: '',
        name: 'Welcome',
        component: () => import('pages/Index.vue'),
      },
      ...pollRoutes,
      ...organizationRoutes,
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
