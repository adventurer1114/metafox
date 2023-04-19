import { useGlobal } from '@metafox/framework';
import * as React from 'react';
import { EmbedPhotoAlbumInFeedItemProps } from '../../../types';

export default function EmbedPhotoAlbumInFeedItemView({
  item,
  photos,
  itemView = 'feedPhotoGrid.embedItem.insideFeedItem',
  setVisible
}: EmbedPhotoAlbumInFeedItemProps) {
  const { jsxBackend } = useGlobal();
  const ItemView = jsxBackend.get(itemView);

  if (!ItemView || !item) return null;

  const { total_item, id } = item;

  const filteredPhotos = photos.filter(item => !item.error);

  if (!filteredPhotos.length && setVisible) setVisible(false);

  return (
    <ItemView
      photo_album={id}
      items={filteredPhotos}
      total_item={total_item}
      data-testid="embedview"
    />
  );
}
