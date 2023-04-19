/**
 * @type: skeleton
 * name: reaction.itemView.reactedUser.skeleton
 */
import { ItemView } from '@metafox/ui';
import { Skeleton } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  const classes = useStyles();

  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <div className={classes.itemOuter}>
        <div className={classes.itemSmallInner}>
          <div className={classes.itemMainContent}>
            <div className={classes.itemSmallMedia}>
              <Skeleton variant="circular" width={48} height={48} />
            </div>
            <div className={classes.userSmallInfo}>
              <Skeleton variant="text" width={100} />
            </div>
          </div>
        </div>
      </div>
    </ItemView>
  );
}
