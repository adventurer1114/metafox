/**
 * @type: skeleton
 * name: quiz.itemView.mainCard.skeleton
 */
import {
  ImageSkeleton,
  ItemMedia,
  ItemText,
  ItemTitle,
  ItemView
} from '@metafox/ui';
import { Skeleton } from '@mui/material';
import React from 'react';
import useStyles from './styles';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  const classes = useStyles();

  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps}>
      <ItemMedia>
        <ImageSkeleton ratio="11" />
      </ItemMedia>
      <ItemText>
        <ItemTitle>
          <Skeleton width={'100%'} />
        </ItemTitle>
        <div className={classes.itemMinor}>
          <Skeleton width={160} />
        </div>
        <div className={classes.itemStatistic}>
          <Skeleton width={160} />
        </div>
      </ItemText>
    </ItemView>
  );
}
