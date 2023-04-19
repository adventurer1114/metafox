import { GlobalState } from '@metafox/framework';
import { createSelector } from 'reselect';

const getCreateGroupFormValue = (state: GlobalState) => {
  return state.formValues['groups.groups'];
};

export const getCreateGroupFormValueSelector = createSelector(
  getCreateGroupFormValue,
  item => item
);
