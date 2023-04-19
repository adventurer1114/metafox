import { isString } from 'lodash';

export function capitalizeWord(s) {
  if (s && isString(s)) return s.substr(0, 1).toUpperCase() + s.substr(1);
}
