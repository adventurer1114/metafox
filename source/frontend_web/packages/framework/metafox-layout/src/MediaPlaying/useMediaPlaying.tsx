/**
 * @type: service
 * name: useMediaPlaying
 */

import Context from './Context';
import { get, has } from 'lodash';
import { useContext } from 'react';

export default function useMediaPlaying(
  id: string
): [boolean, (x: boolean) => void] {
  const [value, setValue] = useContext(Context);
  const playing = !!get(value, id);

  const setPlaying = (value: boolean) => {
    const isCurrentMedia = has(value, id);

    if (!isCurrentMedia && !value) return;

    setValue({ [id]: value });
  };

  return [playing, setPlaying];
}
