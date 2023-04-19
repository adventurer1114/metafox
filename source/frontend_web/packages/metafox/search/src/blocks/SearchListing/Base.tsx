import { usePageParams } from '@metafox/layout';
import { Box } from '@mui/material';
import * as React from 'react';
import Item from './Item';
import Sections from './Sections';

export default function SearchListing(props) {
  const pageParams = usePageParams();
  const { view } = pageParams;

  return (
    <Box sx={{ display: 'flex', justifyContent: 'center' }}>
      {view === 'all' ? <Sections {...props} /> : <Item {...props} />}
    </Box>
  );
}
