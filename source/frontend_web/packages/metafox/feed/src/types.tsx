import {
  EmbedItemInFeedItemProps,
  ItemEmbedObjectShape,
  ItemShape,
  ItemViewProps,
  SimpleUserShape
} from '@metafox/ui';

export type FeedItemShape = {
  info?: string;
  status?: string;
  embed_object?: string;
  status_background?: string | false;
  most_reactions?: any[];
  related_comments: string[];
  related_comments_statistic?: Record<string, any>;
  tagged_friends?: string[];
  item_type: string;
  user_reacted?: string;
  parent_user?: string;
  item_id: number;
  invisible: true;
  isPreFetchingComment?: boolean;
  preFetchingComment?: Record<string, any>;
  is_hidden?: boolean;
  is_hidden_all_user?: boolean;
  is_hidden_all_owner?: boolean;
  is_snooze_user?: boolean;
  is_snooze_owner?: boolean;
  is_just_hide?: boolean;
  total_friends_tagged?: number;
  text: string;
  like_phrase?: string;
  location?: { address: string };
  description?: string;
  _identity: string;
  pins?: any[];
} & ItemShape;

export type ManageHiddenItemShape = ItemShape & {
  unHide?: 1 | 0;
};

export type FeedItemViewState = {
  menuOpened: boolean;
  commentOpened: boolean;
  commentFocused: number;
};

export type FeedItemActions = {
  viewMoreComments: () => void;
};

export type ManageHiddenItemActions = {};

export type ManageHiddenItemProps = ItemViewProps<
  ManageHiddenItemShape,
  ManageHiddenItemActions
> & {
  isFieldName?: string;
  modification_date?: string;
};

export type FeedItemViewProps = ItemViewProps<
  FeedItemShape,
  FeedItemActions,
  FeedItemViewState
> & {
  tagged_friends?: SimpleUserShape[];
  parent_user?: SimpleUserShape;
  embed_object?: ItemEmbedObjectShape;
};

export type EmbedFeedInFeedItemProps = EmbedItemInFeedItemProps<FeedItemShape>;

export type AppState = {
  entities: {
    feed: Record<string, FeedItemShape>;
  };
};
