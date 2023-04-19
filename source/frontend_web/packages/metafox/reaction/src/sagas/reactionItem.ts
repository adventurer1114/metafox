/**
 * @type: saga
 * name: reaction.reactionItem
 */

import {
  getGlobalContext,
  getItem,
  LocalAction,
  patchEntity
} from '@metafox/framework';
import { takeLatest } from 'redux-saga/effects';

type ReactionResult = {
  feed_id: number;
  most_reactions: any[];
  user_reacted: any;
  is_liked: boolean;
  total_like: number;
};

const reactionReg = /(preaction.entities.preaction.)(\d+)/gm;

function* updateItem(identity: string, result: ReactionResult) {
  const item = yield* getItem(identity);
  const most_reactions = result.most_reactions.map(
    x => `preaction.entities.preaction.${x.id}`
  );
  const user_reacted = result.user_reacted
    ? `preaction.entities.preaction.${result.user_reacted.id}`
    : undefined;
  const total_like = result.total_like;
  const statistic = item.statistic
    ? {
        ...item.statistic,
        total_like
      }
    : { total_like };

  yield* patchEntity(identity, {
    statistic,
    is_liked: result.is_liked,
    most_reactions,
    user_reacted
  });
}

function* updateFeed(result: ReactionResult) {
  const identity = `feed.entities.feed.${result.feed_id}`;
  const item = yield* getItem(identity);
  const most_reactions = result.most_reactions.map(
    x => `preaction.entities.preaction.${x.id}`
  );
  const user_reacted = result.user_reacted
    ? `preaction.entities.preaction.${result.user_reacted.id}`
    : undefined;
  const total_like = result.total_like;
  const statistic = item.statistic
    ? { ...item.statistic, total_like }
    : { total_like };

  if (!item) return;

  yield* patchEntity(identity, {
    statistic,
    is_liked: result.is_liked,
    most_reactions,
    user_reacted
  });
}

function* reactionItem(
  action: LocalAction<{
    identity: string;
    reaction_id: string;
  }>
) {
  const currentReaction = [0, 0, 0, 0, 0, 0, 0];
  const { identity, reaction_id } = action.payload;
  const item = yield* getItem(identity);
  const { apiClient } = yield* getGlobalContext();

  const { like_type_id, item_type, like_item_id, resource_name, id, item_id } =
    item;

  const user_reacted = `preaction.entities.preaction.${reaction_id}`;
  const total_like = item.statistic?.total_like ?? 0;
  const beRemoved = item.user_reacted === user_reacted && item.is_liked;
  const beAdded = !item.is_liked;
  const beChanged = item.user_reacted !== user_reacted && item.is_liked;
  const newValue = {
    is_liked: beRemoved ? false : true,
    statistic: { ...item.statistic },
    user_reacted: beRemoved ? undefined : user_reacted,
    most_reactions: item.most_reactions || []
  };

  if (beRemoved) {
    newValue.statistic.total_like = total_like - 1;
  } else if (beAdded) {
    newValue.statistic.total_like = total_like + 1;
  }

  // improve ux
  if (
    total_like < 4 &&
    (total_like === item.most_reactions?.length ||
      !item.most_reactions?.includes(user_reacted))
  ) {
    newValue.most_reactions = [];
    item.most_reactions?.length &&
      item.most_reactions.forEach(react => {
        currentReaction[parseInt(react?.replace(reactionReg, '$2'))] +=
          total_like - item.most_reactions.length + 1;
      });

    beAdded && currentReaction[reaction_id]++;
    beRemoved && currentReaction[reaction_id]--;

    if (beChanged) {
      currentReaction[reaction_id]++;
      currentReaction[parseInt(item.user_reacted.replace(reactionReg, '$2'))]--;
    }

    currentReaction.forEach((item, index) => {
      if (item) {
        newValue.most_reactions.push(`preaction.entities.preaction.${index}`);
      }
    });
  }

  yield* patchEntity(identity, newValue);

  const sendData = {
    item_id: like_item_id || item_id || id,
    item_type: like_type_id || item_type || resource_name
  };

  try {
    const response = beRemoved
      ? yield apiClient.request({
          url: '/like',
          method: 'DELETE',
          params: sendData
        })
      : yield apiClient.request({
          url: '/like',
          method: 'POST',
          data: {
            ...sendData,
            reaction_id
          }
        });
    const result = response.data.data;
    yield* updateItem(identity, result);

    if (result.feed_id) {
      yield* updateFeed(result);
    }

    const itemPreaction = yield* getItem(user_reacted);

    const dataPreaction = result.most_reactions.filter(
      x => x.id === itemPreaction.id
    )[0];

    if (itemPreaction && dataPreaction) {
      const entityPreaction = { ...itemPreaction, icon: dataPreaction.icon };

      yield* patchEntity(user_reacted, entityPreaction);
    }
  } catch (err) {
    //
  }
}

const sagas = [takeLatest('reactionItem', reactionItem)];

export default sagas;
