/**
 * @type: reducer
 * name: pageMeta
 */
import { PageMetaShape } from '@metafox/layout';
import { createReducer } from '@reduxjs/toolkit';
import { PageMetaDataState } from '../types';

type State = PageMetaDataState;

type UpdateAction = {
  type: 'pageMeta/put';
  payload: {
    id: string;
    data: PageMetaShape;
  };
};

export default createReducer<PageMetaDataState>({}, builder => {
  builder.addCase('pageMeta/put', (state: State, { payload }: UpdateAction) => {
    state[payload.id] = payload.data;
  });
});
