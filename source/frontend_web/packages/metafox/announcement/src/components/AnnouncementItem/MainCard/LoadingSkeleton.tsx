/**
 * @type: skeleton
 * name: announcement.itemView.mainCard.skeleton
 */

import { ItemTitle, ItemView } from '@metafox/ui';
import { Skeleton } from '@mui/material';
import React from 'react';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <ItemTitle>
        <Skeleton />
        <Skeleton />
      </ItemTitle>
      <Skeleton />
      <Skeleton />
    </ItemView>
  );
}
