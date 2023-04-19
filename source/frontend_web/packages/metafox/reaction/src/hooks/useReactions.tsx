import { GlobalState } from '@metafox/framework';
import { useSelector } from 'react-redux';
import { createSelector } from 'reselect';

const getReactions = (state: GlobalState) => state.preaction.data.reactions;

const getReactionSelector = createSelector(getReactions, data => data);

export default function useReactions() {
  return useSelector(getReactionSelector);
}
