import { useGlobal } from '@metafox/framework';
import { EmbedPhotoInFeedItemProps } from '@metafox/photo/types';
import * as React from 'react';

export default function EmbedPhotoInFeedItemView({
  item,
  itemView = 'feedPhotoGrid.embedItem.insideFeedItem',
  feed
}: EmbedPhotoInFeedItemProps) {
  const { jsxBackend } = useGlobal();
  const ItemView = jsxBackend.get(itemView);

  if (!ItemView || !item) return null;

  const isUpdateAvatar = feed?.type_id === 'user_update_avatar';

  return (
    <ItemView
      isUpdateAvatar={isUpdateAvatar}
      photos={[item]}
      total_photo={1}
      data-testid="embedview"
    />
  );
}
