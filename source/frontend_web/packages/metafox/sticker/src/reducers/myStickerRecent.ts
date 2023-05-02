import produce, { Draft } from 'immer';
import { uniq } from 'lodash';
import { AppState } from '../types';

export default produce(
  (draft: Draft<AppState['myStickerRecent']>, action) => {
    switch (action.type) {
      case 'sticker/myStickerRecent/FULFILL':
        draft.data = action.payload.data;
        break;
      case 'sticker/myStickerRecent/push':
        draft.data.unshift(action.payload.data);
        draft.data = uniq(draft.data);
        break;
      default:
        return draft;
    }
  },
  {
    data: [],
    loaded: false,
    loading: false
  }
);