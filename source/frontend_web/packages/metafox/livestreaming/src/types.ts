import { BlockViewProps } from '@metafox/framework';
import {
  EmbedItemInFeedItemProps,
  ItemShape,
  ItemViewProps,
  ItemExtraShape
} from '@metafox/ui';

export interface LivestreamItemShape extends ItemShape {
  title: string;
  description: string;
  location: Record<string, any>;
  categories: any;
  text: string;
  attach_photos: string[];
  tags: string[];
  is_sold: boolean;
  thumbnail_url: string;
  is_streaming?: boolean | number;
  stream_key: string;
  video_url: string;
  is_owner: boolean;
  extra: ItemExtraShape & {
    can_invite?: boolean;
    can_payment?: boolean;
    can_message?: boolean;
  };
}

export interface LivestreamImageItemShape extends ItemShape {
  image: Record<string, any>;
}

export type LivestreamItemActions = {
  updateViewer: () => void;
  removeViewer: () => void;
};

export type LivestreamItemState = {
  menuOpened?: boolean;
};

export type LivestreamItemProps = ItemViewProps<
  LivestreamItemShape,
  LivestreamItemActions,
  LivestreamItemState
> & {
  categories: any;
};

export type EmbedLivestreamInFeedItemProps =
  EmbedItemInFeedItemProps<LivestreamItemShape>;

export type LivestreamDetailViewProps = LivestreamItemProps &
  BlockViewProps & {
    isModalView?: boolean;
  };
