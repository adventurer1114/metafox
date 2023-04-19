import { BlockViewProps } from '@metafox/framework';
import { EmbedItemInFeedProps, ItemShape, ItemViewProps } from '@metafox/ui';

export interface VideoItemShape extends ItemShape {
  title: string;
  destination: string;
  video_url: string;
  is_processing?: boolean;
}

export interface VideoItemShapeDialog extends ItemShape {
  embed_code: string;
  destination: string;
  video_url: string;
  album?: any;
  categories?: any;
}

export interface VideoItemShapeDialogProps extends BlockViewProps {
  item: VideoItemShapeDialog;
  identity: string;
}

export type VideoItemProps = ItemViewProps<VideoItemShape>;

export type VideoItemState = {
  menuOpened?: boolean;
};

export type EmbedVideoInFeedItemProps = EmbedItemInFeedProps<VideoItemShape>;

export type AppState = {};
