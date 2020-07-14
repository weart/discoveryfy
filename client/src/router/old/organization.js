import OrganizationCreate from 'src/components/organization/Create';
import OrganizationUpdate from 'src/components/organization/Update';
import Polls from 'src/pages/Polls';

export default [
  {
    name: 'OrganizationCreate',
    path: '/organizations/create',
    component: OrganizationCreate,
    meta: {
      breadcrumb: [
        { label: 'Organization List', icon: 'whatshot', to: { name: 'OrganizationList' } },
        { label: 'New Organization', icon: 'whatshot' },
      ],
    },
  },
  {
    name: 'OrganizationUpdate',
    path: '/organizations/edit/:id',
    component: OrganizationUpdate,
    meta: {
      breadcrumb: [
        { label: 'Organization List', icon: 'whatshot', to: { name: 'OrganizationList' } },
        { label: 'Edit Organization', icon: 'whatshot' },
      ],
    },
  },
  {
    name: 'OrganizationPolls',
    path: '/organization/:id/polls',
    component: Polls,
    meta: {
      breadcrumb: [
        { label: 'Organization List', icon: 'whatshot', to: { name: 'OrganizationList' } },
        { label: 'Polls', icon: 'whatshot' },
      ],
    },
  },
];
