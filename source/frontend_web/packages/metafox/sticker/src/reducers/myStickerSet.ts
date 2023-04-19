import produce, { Draft } from 'immer';
import { AppState } from '../types';

export default produce(
  (draft: Draft<AppState['myStickerSet']>, action) => {
    switch (action.type) {
      case 'sticker/myStickerSet/INIT':
        draft.loading = true;
        break;
      case 'sticker/myStickerSet/FULFILL':
        draft.data = action.payload.data;
        draft.loaded = true;
        draft.loading = false;
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
