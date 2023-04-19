import { useGlobal } from '@metafox/framework';
import * as React from 'react';
import { EmbedPhotoSetInFeedItemProps } from '../../../types';

export default function EmbedPhotoSetInFeedItemView({
  item,
  photos,
  itemView = 'feedPhotoGrid.embedItem.insideFeedItem',
  setVisible
}: EmbedPhotoSetInFeedItemProps) {
  const { jsxBackend } = useGlobal();
  const ItemView = jsxBackend.get(itemView);
  const ItemViewNotFound = jsxBackend.get(
    'itemNotFound.embedItem.insideFeedItem'
  );

  if (!ItemView || !item) return null;

  const { id, remain_photo } = item;

  if ((!photos || !photos.length) && setVisible) {
    setVisible(false);
  }

  const filteredPhotos = photos.filter(item => !item.error);

  if (!filteredPhotos.length && ItemViewNotFound) {
    const photoError = photos[0];

    return (
      <ItemViewNotFound
        description={photoError?.message}
        title={photoError?.title}
      />
    );
  }

  return (
    <ItemView
      photo_set={id}
      photos={filteredPhotos}
      total_photo={remain_photo}
      data-testid="embedview"
    />
  );
}
