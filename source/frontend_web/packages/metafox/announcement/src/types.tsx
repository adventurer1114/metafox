import { ItemShape, ItemViewProps } from '@metafox/ui';

export interface AnnouncementItemShape extends ItemShape {
  title: string;
  description: string;
  text: string;
  [key: string]: any;
}

export type AnnouncementItemProps = ItemViewProps<AnnouncementItemShape>;
