import produce, { Draft } from 'immer';
import { AppState } from '../types';

export default produce((draft: Draft<AppState['stickerSet']>, action) => {
  switch (action.type) {
    default:
      return draft;
  }
}, {
  data: [],
  loaded: false
});