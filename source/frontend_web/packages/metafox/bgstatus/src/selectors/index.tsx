import { GlobalState } from '@metafox/framework';
import { createSelector } from 'reselect';
import { AppState } from '../types';

const getBgStatus = (state: GlobalState): AppState => state.bgstatus;

export const getBgStatusSelector = createSelector(getBgStatus, data => data);
