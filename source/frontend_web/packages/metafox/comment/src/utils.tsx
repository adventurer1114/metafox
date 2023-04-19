import { SortTypeValue, SortTypeModeValue } from '@metafox/comment/types';
import {
  SORT_OLDEST,
  SORT_RELEVANT,
  SORT_MODE_ASC,
  SORT_MODE_DESC
} from '@metafox/comment';

export const getValueSortTypeMode = (type: SortTypeValue) =>
  ([SORT_OLDEST, SORT_RELEVANT].includes(type) ? SORT_MODE_ASC : SORT_MODE_DESC);

export const getKeyDataBySortTypeMode = (
  mode: SortTypeModeValue,
  init: boolean = false
) => {
  const keyPrefix = init ? '_' : '_full_';

  return mode === SORT_MODE_ASC
    ? `${keyPrefix}oldest_related_comments`
    : `${keyPrefix}newest_related_comments`;
};

export const getKeyDataBySortType = (
  type: SortTypeValue,
  init: boolean = false
) => {
  const mode = getValueSortTypeMode(type);

  return getKeyDataBySortTypeMode(mode, init);
};

export const getDataBySortTypeMode = (
  data: Record<string, any>,
  mode: SortTypeModeValue,
  init: boolean = false
) => {
  if (!data) return;

  const key = getKeyDataBySortTypeMode(mode, init);

  return data[key];
};

export const getDataBySortType = (
  data: Record<string, any>,
  type: SortTypeValue,
  init: boolean = false
) => {
  if (!data) return;

  const key = getKeyDataBySortType(type, init);

  return data[key];
};
