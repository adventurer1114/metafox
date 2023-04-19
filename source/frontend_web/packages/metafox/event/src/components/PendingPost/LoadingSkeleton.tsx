/**
 * @type: skeleton
 * name: event.itemView.pendingPost.skeleton
 */

import { ItemView } from '@metafox/ui';
import { Box, Skeleton } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  const classes = useStyles();

  return (
    <ItemView
      testid={'event-pendingPost-loadingSkeleton'}
      wrapAs={wrapAs}
      wrapProps={wrapProps}
    >
      <div className={classes.header}>
        <Skeleton variant="circular" width={40} height={40} />
        <div className={classes.headerInfo}>
          <div>
            <Skeleton variant="text" component="div" />
          </div>
          <div className={classes.privacyBlock}>
            <Skeleton variant="text" width={120} />
          </div>
        </div>
      </div>
      <Box>
        <Skeleton variant="text" />
        <Skeleton variant="text" />
        <Skeleton variant="text" />
      </Box>
    </ItemView>
  );
}
