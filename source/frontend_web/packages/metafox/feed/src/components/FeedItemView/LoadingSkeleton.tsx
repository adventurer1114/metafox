/**
 * @type: skeleton
 * name: feed.itemView.mainCard.skeleton
 */
import { ItemView } from '@metafox/ui';
import { Skeleton } from '@mui/material';
import React from 'react';
import useStyles from './styles';
import { AvatarWrapper } from './FeedItemView';

export function LoadingSkeleton({ wrapAs, wrapProps }) {
  const classes = useStyles();

  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <div className={classes.header}>
        <AvatarWrapper>
          <Skeleton variant="circular" width={40} height={40} />
        </AvatarWrapper>
        <div className={classes.headerInfo}>
          <div>
            <Skeleton variant="text" component="div" />
          </div>
          <div className={classes.privacyBlock}>
            <Skeleton variant="text" width={120} />
          </div>
        </div>
      </div>
      <div className={classes.contentSkeleton}>
        <Skeleton variant="text" />
        <Skeleton variant="text" />
        <Skeleton variant="text" />
      </div>
    </ItemView>
  );
}

export default LoadingSkeleton;
