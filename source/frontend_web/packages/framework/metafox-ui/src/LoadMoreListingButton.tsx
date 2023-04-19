import { useGlobal } from '@metafox/framework';
import React from 'react';
import { styled, Link } from '@mui/material';

export interface LoadMoreListingButtonProps {
  'data-testid'?: string;
  handleClick: () => void;
  message?: string;
}
const Wrapper = styled(Link, {
  name: 'PaginationWrapper'
})(({ theme }) => ({
  display: 'flex',
  justifyContent: 'center',
  alignItems: 'center',
  padding: theme.spacing(2)
}));

export default function LoadMoreButton({
  handleClick,
  message = 'load_more',
  'data-testid': testid = 'pagination'
}: LoadMoreListingButtonProps) {
  const { i18n } = useGlobal();

  return (
    <Wrapper color="primary" variant="body2" onClick={handleClick}>
      {i18n.formatMessage({
        id: message
      })}
    </Wrapper>
  );
}
