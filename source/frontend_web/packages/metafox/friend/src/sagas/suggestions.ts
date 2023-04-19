/**
 * @type: saga
 * name: friend.saga.suggestions
 */

import { getGlobalContext, GlobalState } from '@metafox/framework';
import { getImageSrc } from '@metafox/utils';
import { debounce, put, select } from 'redux-saga/effects';

type QueryAction = {
  type: string;
  payload: {
    text: string;
    item_id: number;
    item_type: string;
    excludeIds?: Array<number>;
    is_full?: boolean;
  };
};

const selectSuggestion = (state: GlobalState, text: string) => {
  return state.friend.suggestions[text];
};

export function* query(action: QueryAction) {
  const {
    text = '',
    item_id = null,
    item_type = null,
    excludeIds,
    is_full
  } = action.payload;
  const prev = yield select(selectSuggestion, text);

  if (prev?.loaded) return;

  const { apiClient } = yield* getGlobalContext();
  const response = yield apiClient.request({
    method: 'get',
    url: '/friend/tag-suggestion',
    params: {
      q: text || undefined,
      limit: 3,
      item_id,
      item_type,
      excluded_ids: excludeIds,
      is_full
    }
  });

  const result = response.data?.data;

  const data = Array.isArray(result)
    ? result.map(item => ({
        id: item.id,
        image: getImageSrc(item.avatar, '240'),
        label: item.full_name,
        resource_name: item.resource_name,
        module_name: item.module_name
      }))
    : [];

  yield put({ type: 'friend/suggestions/FULFILL', payload: { text, data } });
}

const sagas = [debounce(500, 'friend/suggestions/LOAD', query)];

export default sagas;
