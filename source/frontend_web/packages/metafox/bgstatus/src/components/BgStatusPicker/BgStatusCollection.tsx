import { Grid, Typography, Box } from '@mui/material';
import React from 'react';
import { BgStatusCollectionShape } from '../../types';
import Item from './BgStatusItem';

export type BgStatusCollectionProps = {
  data: BgStatusCollectionShape;
  classes: Record<string, string>;
  onSelectItem: (item: unknown) => void;
  selectedId?: number;
};

const MAXIMUM_NUMBER_MORE = 10;

export default function BgStatusCollection(props: BgStatusCollectionProps) {
  const { data, classes, onSelectItem, selectedId } = props;
  const [isFull, setIsFull] = React.useState(false);

  if (!data.backgrounds?.length) return null;

  return (
    <Box sx={{ '&:not(:last-child)': { mb: 3 } }}>
      <Typography variant={'h4'} mb={2}>
        {data?.name}
      </Typography>
      <Grid container spacing={1}>
        {data.backgrounds.map((item, index) => (
          <Grid item key={item.id.toString()} md={2.4} sm={4} xs={6}>
            <Item
              item={item}
              onClick={() => onSelectItem(item)}
              classes={classes}
              isHide={!isFull && index >= MAXIMUM_NUMBER_MORE}
              selected={selectedId === item.id}
              onClickLoadMore={
                !isFull && index + 1 === MAXIMUM_NUMBER_MORE
                  ? () => setIsFull(true)
                  : undefined
              }
              labelLoadmore={`+${
                data.backgrounds.length - MAXIMUM_NUMBER_MORE
              }`}
            />
          </Grid>
        ))}
      </Grid>
    </Box>
  );
}
