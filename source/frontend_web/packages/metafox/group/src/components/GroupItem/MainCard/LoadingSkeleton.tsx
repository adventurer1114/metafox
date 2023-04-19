/**
 * @type: skeleton
 * name: group.itemView.mainCard.skeleton
 */
import {
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  ImageSkeleton
} from '@metafox/ui';
import { Skeleton } from '@mui/material';
import * as React from 'react';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <ItemMedia>
        <ImageSkeleton ratio="13" />
      </ItemMedia>
      <ItemText>
        <ItemTitle>
          <Skeleton width={'100%'} />
        </ItemTitle>
        <ItemSummary>
          <Skeleton width={160} />
        </ItemSummary>
      </ItemText>
    </ItemView>
  );
}
