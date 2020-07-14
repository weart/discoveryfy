import TrackList from '../components/track/List';
import TrackCreate from '../components/track/Create';
import TrackUpdate from '../components/track/Update';
import TrackShow from '../components/track/Show';

export default [
  {
    name: 'TrackList',
    path: '/tracks/',
    component: TrackList,
    meta: {
      breadcrumb: [{ label: 'Track List', icon: 'whatshot' }],
    },
  },
  {
    name: 'TrackCreate',
    path: '/tracks/create',
    component: TrackCreate,
    meta: {
      breadcrumb: [
        { label: 'Track List', icon: 'whatshot', to: { name: 'TrackList' } },
        { label: 'New Track', icon: 'whatshot' },
      ],
    },
  },
  {
    name: 'TrackUpdate',
    path: '/tracks/edit/:id',
    component: TrackUpdate,
    meta: {
      breadcrumb: [
        { label: 'Track List', icon: 'whatshot', to: { name: 'TrackList' } },
        { label: 'Edit Track', icon: 'whatshot' },
      ],
    },
  },
  {
    name: 'TrackShow',
    path: '/tracks/show/:id',
    component: TrackShow,
    meta: {
      breadcrumb: [
        { label: 'Track List', icon: 'whatshot', to: { name: 'TrackList' } },
        { label: 'Show Track', icon: 'whatshot' },
      ],
    },
  },
];
