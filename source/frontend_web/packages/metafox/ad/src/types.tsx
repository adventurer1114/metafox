import {
  EmbedItemInFeedItemProps,
  ItemShape,
  ItemViewProps
} from '@metafox/ui';

export interface AdItemShape extends ItemShape {
  title: string;
  description: string;
}

export type AdItemProps = ItemViewProps<AdItemShape>;

export type EmbedAdItemInFeedItemProps = EmbedItemInFeedItemProps<AdItemShape>;

export interface AppState {
  entities: {
    blog: Record<string, AdItemShape>;
  };
}
