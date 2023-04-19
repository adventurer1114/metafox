/**
 * @type: skeleton
 * name: event.itemView.inviteCard.skeleton
 */

import {
  ItemAction,
  ItemMedia,
  ItemText,
  ItemTitle,
  ItemView
} from '@metafox/ui';
import { Skeleton } from '@mui/material';
import React from 'react';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  return (
    <ItemView testid={'LoadingSkeleton'} wrapAs={wrapAs} wrapProps={wrapProps}>
      <ItemMedia>
        <Skeleton variant="avatar" width={48} height={48} />
      </ItemMedia>
      <ItemText>
        <ItemTitle>
          <Skeleton variant="text" width="50%" />
        </ItemTitle>
        <Skeleton variant="text" width="120px" />
      </ItemText>
      <ItemAction>
        <Skeleton width={60} height={40} />
      </ItemAction>
    </ItemView>
  );
}
