import { ItemSummary, ItemText, ItemTitle, ItemView } from '@metafox/ui';
import { Skeleton } from '@mui/material';
import * as React from 'react';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <ItemText>
        <ItemTitle>
          <Skeleton variant="text" width="60%" />
        </ItemTitle>
        <ItemSummary>
          <Skeleton variant="text" width="30%" />
        </ItemSummary>
      </ItemText>
    </ItemView>
  );
}
