/**
 * @type: skeleton
 * name: saved_collection_list.itemView.mainCard.skeleton
 * title: Main Card
 */
import {
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  LineIcon
} from '@metafox/ui';
import { Skeleton } from '@mui/material';
import React from 'react';

export default function LoadingSkeleton({ wrapAs, wrapProps }) {
  return (
    <ItemView wrapAs={wrapAs} wrapProps={wrapProps} testid="LoadingSkeleton">
      <ItemMedia>
        <LineIcon
          icon="ico-folder-alt-o"
          color="textPrimary"
          sx={{ fontSize: '1rem' }}
        />
      </ItemMedia>
      <ItemText>
        <ItemTitle>
          <Skeleton variant="text" width="50%" height="20" />
        </ItemTitle>
        <ItemSummary>
          <Skeleton variant="text" width="30%" height="20" />
        </ItemSummary>
      </ItemText>
    </ItemView>
  );
}
