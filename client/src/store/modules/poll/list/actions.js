import { types } from './mutation_types';
import { getItemsCommon, getSelectItemsCommon } from '../../../../common/store/list/actions';

const hydraPrefix = 'hydra:';

export const getItems = (state, options) =>
  getItemsCommon(
    state,
    { ...{ page: 'polls', params: {} }, ...options },
    { types, hydraPrefix },
  );

export const getSelectItems = (state, options) =>
  getSelectItemsCommon(
    state,
    { ...{ page: 'polls', params: { properties: ['id', 'name'] } }, ...options },
    { types, hydraPrefix },
  );
