/**
 * @type: skeleton
 * name: shortcut.itemView.smallCard.skeleton
 */
import { ItemMedia, ItemText, ItemTitle, ItemView } from '@metafox/ui';
import { Skeleton } from '@mui/material';
import React from 'react';

const LoadingSkeleton = ({ wrapAs, wrapProps }) => {
  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <ItemMedia>
        <Skeleton variant="avatar" width={32} height={32} />
      </ItemMedia>
      <ItemText>
        <ItemTitle>
          <Skeleton variant="text" width={100} height={16} />
        </ItemTitle>
      </ItemText>
    </ItemView>
  );
};

export default LoadingSkeleton;
