/**
 * @type: reducer
 * name: bgstatus
 */
import produce, { Draft } from 'immer';
import { AppState } from '../types';

export default produce(
  (draft: Draft<AppState>, action) => {
    switch (action.type) {
      case 'bgstatus/FULFILL': {
        const { payload } = action;
        draft.collections = payload.collections;
        draft.defaultItems = payload.defaultItems;
        draft.loaded = true;
        break;
      }
    }
  },
  {
    loaded: false,
    collections: [],
    defaultItems: []
  }
);
