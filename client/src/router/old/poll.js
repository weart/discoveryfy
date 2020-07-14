import PollList from 'src/components/poll/List';
import PollCreate from 'src/components/poll/Create';
import PollUpdate from 'src/components/poll/Update';
import PollShow from 'src/components/poll/Show';

export default [
  {
    name: 'PollList',
    path: '/polls/',
    component: PollList,
    meta: {
      breadcrumb: [{ label: 'Poll List', icon: 'whatshot' }],
    },
  },
  {
    name: 'PollCreate',
    path: '/polls/create',
    component: PollCreate,
    meta: {
      breadcrumb: [
        { label: 'Poll List', icon: 'whatshot', to: { name: 'PollList' } },
        { label: 'New Poll', icon: 'whatshot' },
      ],
    },
  },
  {
    name: 'PollUpdate',
    path: '/polls/edit/:id',
    component: PollUpdate,
    meta: {
      breadcrumb: [
        { label: 'Poll List', icon: 'whatshot', to: { name: 'PollList' } },
        { label: 'Edit Poll', icon: 'whatshot' },
      ],
    },
  },
  {
    name: 'PollShow',
    path: '/polls/show/:id',
    component: PollShow,
    meta: {
      breadcrumb: [
        { label: 'Poll List', icon: 'whatshot', to: { name: 'PollList' } },
        { label: 'Show Poll', icon: 'whatshot' },
      ],
    },
  },
];
