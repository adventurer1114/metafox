import { GlobalState } from '@metafox/framework';
import { useSelector } from 'react-redux';
import { getMyStickerRecent } from '../selectors';
import { AppState } from '../types';

export default function useMyStickerSet() {
  return useSelector<GlobalState, AppState['myStickerRecent']>(state =>
    getMyStickerRecent(state)
  );
}