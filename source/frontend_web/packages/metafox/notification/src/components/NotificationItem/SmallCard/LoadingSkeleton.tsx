/**
 * @type: skeleton
 * name: notification.itemView.smallCard.skeleton
 */

import { ItemMedia, ItemSummary, ItemText, ItemView } from '@metafox/ui';
import { Skeleton } from '@mui/material';
import React from 'react';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <ItemMedia>
        <Skeleton variant="circular" width={48} height={48} />
      </ItemMedia>
      <ItemText>
        <ItemSummary>
          <Skeleton variant="text" width={240} />
          <Skeleton variant="text" width={240} />
        </ItemSummary>
        <Skeleton variant="text" width={40} />
      </ItemText>
    </ItemView>
  );
}
