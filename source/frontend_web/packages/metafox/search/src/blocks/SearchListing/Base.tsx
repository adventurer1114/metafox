import { usePageParams } from '@metafox/layout';
import { Box, styled } from '@mui/material';
import * as React from 'react';
import Item from './Item';
import Sections from './Sections';

const SearchWrapper = styled(Box, { name: 'SearchWrapper' })(({ theme }) => ({
  [theme.breakpoints.up('sm')]: {
    display: 'flex',
    justifyContent: 'center'
  }
}));

export default function SearchListing(props) {
  const pageParams = usePageParams();
  const { view } = pageParams;

  React.useEffect(() => {
    window.scrollTo(0, 0);
  }, [view]);

  return (
    <SearchWrapper>
      {view === 'all' ? <Sections {...props} /> : <Item {...props} />}
    </SearchWrapper>
  );
}
