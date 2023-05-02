/**
 * @type: saga
 * name: livestreaming.saga.updateStatistic
 */

import {
  getItem,
  ItemLocalAction,
  getSession,
  patchEntity,
  getGlobalContext,
  fulfillEntity
} from '@metafox/framework';
import { takeLatest } from 'redux-saga/effects';
import { pickBy } from 'lodash';

function* updateStatistic(
  action: ItemLocalAction & {
    payload: { statistic: Record<string, any>; most_reactions: Array<any> };
  }
) {
  const { identity, statistic, most_reactions } = action.payload;
  const item = yield* getItem(identity);
  const { loggedIn } = yield* getSession();
  const { normalization } = yield* getGlobalContext();

  if (!item || !item?.is_streaming || !loggedIn) return;

  try {
    let ids_most_reactions;

    if (most_reactions) {
      const result = normalization.normalize(most_reactions);
      ids_most_reactions = result?.ids;
      yield* fulfillEntity(result.data);
    }

    const data = {
      most_reactions: ids_most_reactions,
      statistic: { ...item?.statistic, ...statistic }
    };
    const cleanData = pickBy(data, v => v !== undefined);
    yield* patchEntity(identity, cleanData);
  } catch (error) {}
}

const sagas = [takeLatest('livestreaming/updateStatistic', updateStatistic)];

export default sagas;
