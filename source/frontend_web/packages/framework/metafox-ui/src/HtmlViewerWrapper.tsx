import { Box, styled } from '@mui/material';
import React from 'react';

const name = 'HtmlWrapper';

const Content = styled(Box, { name, slot: 'htmlWrapper' })(({ theme }) => ({
  marginTop: theme.spacing(2),
  '& > p:first-child': {
    marginTop: 0
  },
  '& p + p': {
    marginBottom: theme.spacing(2.5)
  }
}));

export default function HtmlWrapper({ children, ...rest }) {
  return <Content {...rest}>{children}</Content>;
}
