import { Grid } from '@mui/material';
import React from 'react';
import { BgStatusCollectionShape } from '../../types';
import Item from './BgStatusItem';

export type BgStatusCollectionProps = {
  data: BgStatusCollectionShape;
  classes: Record<string, string>;
  onSelectItem: (item: unknown) => void;
};

export default function BgStatusCollection(props: BgStatusCollectionProps) {
  const { data, classes, onSelectItem } = props;

  if (!data.backgrounds?.length) return null;

  return (
    <Grid container spacing={1}>
      {data.backgrounds.map((item, index) => (
        <Grid item key={item.id.toString()} md={4} sm={6} xs={12}>
          <Item
            item={item}
            onClick={() => onSelectItem(item)}
            classes={classes}
          />
        </Grid>
      ))}
    </Grid>
  );
}
