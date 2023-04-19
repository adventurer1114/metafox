import React from 'react';
import { styled } from '@mui/material';

const Wrapper = styled('div', {
  name: 'InstallPanel'
})(({ theme }) => ({
  width: 700,
  backgroundColor: theme.palette.background.paper,
  margin: theme.spacing(6, 0),
  border: theme.mixins.border('secondary'),
  borderRadius: 8,
  position: 'relative',
  boxShadow: theme.shadows[2]
}));

export default function Panel({ children }: { children: any; error?: string }) {
  return <Wrapper>{children}</Wrapper>;
}
