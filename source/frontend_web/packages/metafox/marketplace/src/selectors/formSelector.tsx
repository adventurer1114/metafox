import { GlobalState } from '@metafox/framework';
import { createSelector } from 'reselect';

const getCreateMarketplaceFormValue = (state: GlobalState) => {
  return state.formValues['marketplace.marketplace'];
};

export const getCreateMarketplaceFormValueSelector = createSelector(
  getCreateMarketplaceFormValue,
  item => item
);
