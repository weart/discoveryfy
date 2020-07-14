import VoteList from '../components/vote/List';
import VoteCreate from '../components/vote/Create';
import VoteUpdate from '../components/vote/Update';
import VoteShow from '../components/vote/Show';

export default [
  {
    name: 'VoteList',
    path: '/votes/',
    component: VoteList,
    meta: {
      breadcrumb: [{ label: 'Vote List', icon: 'whatshot' }],
    },
  },
  {
    name: 'VoteCreate',
    path: '/votes/create',
    component: VoteCreate,
    meta: {
      breadcrumb: [
        { label: 'Vote List', icon: 'whatshot', to: { name: 'VoteList' } },
        { label: 'New Vote', icon: 'whatshot' },
      ],
    },
  },
  {
    name: 'VoteUpdate',
    path: '/votes/edit/:id',
    component: VoteUpdate,
    meta: {
      breadcrumb: [
        { label: 'Vote List', icon: 'whatshot', to: { name: 'VoteList' } },
        { label: 'Edit Vote', icon: 'whatshot' },
      ],
    },
  },
  {
    name: 'VoteShow',
    path: '/votes/show/:id',
    component: VoteShow,
    meta: {
      breadcrumb: [
        { label: 'Vote List', icon: 'whatshot', to: { name: 'VoteList' } },
        { label: 'Show Vote', icon: 'whatshot' },
      ],
    },
  },
];
