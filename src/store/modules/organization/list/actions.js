import { types } from './mutation_types';
import { getItemsCommon, getSelectItemsCommon } from '../../../../common/store/list/actions';

const hydraPrefix = 'hydra:';

export const getItems = (state, options) =>
  getItemsCommon(
    state,
    { ...{ page: 'organizations', params: {} }, ...options },
    { types, hydraPrefix },
  );

export const getSelectItems = (state, options) =>
  getSelectItemsCommon(
    state,
    { ...{ page: 'organizations', params: { properties: ['id', 'name'] } }, ...options },
    { types, hydraPrefix },
  );
