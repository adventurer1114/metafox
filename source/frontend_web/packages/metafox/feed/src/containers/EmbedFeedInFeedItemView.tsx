/**
 * @type: embedView
 * name: feed.embedItem.insideFeedItem
 */

import { connect, GlobalState } from '@metafox/framework';
import { get } from 'lodash';
import View from '../components/EmbedFeedInFeedItemView';

const mapStateToProps = (state: GlobalState, { identity }: any) => {
  const item = get(state, identity);

  if (!item) return {};

  return {
    item,
    user: item.user ? get(state, item.user) : undefined,
    parent_user: item.parent_user ? get(state, item.parent_user) : undefined
  };
};

export default connect(mapStateToProps)(View);
