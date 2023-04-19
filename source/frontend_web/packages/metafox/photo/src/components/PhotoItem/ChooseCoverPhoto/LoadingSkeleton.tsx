/**
 * @type: skeleton
 * name: photo.itemView.chooseCoverPhoto.skeleton
 */
import { ItemView } from '@metafox/ui';
import { Skeleton } from '@mui/material';
import React from 'react';
import useStyles from './styles';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  const classes = useStyles();

  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <Skeleton className={classes.root} variant="rectangular" />
    </ItemView>
  );
}
