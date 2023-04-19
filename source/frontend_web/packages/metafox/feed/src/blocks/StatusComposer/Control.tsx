import { LineIcon } from '@metafox/ui';
import { IconButton, styled } from '@mui/material';
import React from 'react';

const StyledButton = styled(IconButton, {
  name: 'StatusControl',
  slot: 'Control'
})(({ theme }) => ({
  width: 32,
  height: 32,
  fontSize: theme.mixins.pxToRem(20),
  [theme.breakpoints.up('sm')]: {
    width: 40,
    height: 40
  }
}));

export default function Control({ icon, ...rest }) {
  return (
    <StyledButton color="primary" {...rest}>
      <LineIcon icon={icon} />
    </StyledButton>
  );
}
