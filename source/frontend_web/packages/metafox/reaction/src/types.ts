export type ReactionItem = {
  resource_name: string;
  module_name: string;
  title: string;
  icon?: string;
  src?: string;
  color?: string;
  ordering: number;
  is_default: boolean;
  id: number;
};

export type ReactionThisItemPopoverProps = {
  identity: string;
  item: string;
  open: boolean;
  anchorEl: any;
};

export type ReactionIconProps = {
  classes?: any;
} & ReactionItem;

export type ReactedUserItemShape = {};

export type MostReactionsProps = {
  handleAction: any;
  data: string[];
  limit?: number;
  total?: number;
  size?: 'md';
  identity: string;
  message?: string;
};

export type ReactedTab = {
  title: string;
  icon: string;
  color: string;
  id: number;
  total_reacted: number;
};

export type PeopleWhoReactionThisProps = {
  identity: string;
  item_type: string;
  item_id: string;
  tabs: ReactedTab[];
  reactIdentity: string;
  reactId?: any;
};

export type AppState = {
  entities: {
    preaction: Record<string, ReactionItem>;
  };
  data: {
    ids: string[];
    unreactedItem?: ReactionItem;
    reactions: ReactionItem[];
  };
  reactedTabs: Record<string, { tabs: ReactedTab[] }>;
};
