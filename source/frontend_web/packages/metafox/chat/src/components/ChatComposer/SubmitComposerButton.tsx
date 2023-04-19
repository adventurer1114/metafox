/**
 * @type: ui
 * name: chatComposer.control.buttonSubmit
 */

import { LineIcon } from '@metafox/ui';
import { styled } from '@mui/material';
import React from 'react';

const WrapperButtonIcon = styled('div', {
  shouldForwardProp: props => props !== 'isContent'
})<{ isContent?: boolean }>(({ theme, isContent }) => ({
  fontSize: theme.spacing(1.875),
  padding: 0,
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  width: '28px',
  height: '28px',
  minWidth: '28px',
  // color:
  //   theme.palette.mode === 'light'
  //     ? theme.palette.grey['700']
  //     : theme.palette.text.primary,
  '& .ico': {
    color: theme.palette.action.disabled
  },
  ...(isContent && {
    cursor: 'pointer',
    '&:hover': {
      backgroundColor: theme.palette.action.hover,
      borderRadius: 50
    },
    '& .ico': {
      color: theme.palette.primary.main
    }
  })
}));

function ButtonSubmitComposer({ rid, handleSubmit, disableSubmit }: any) {
  const handleClick = (event: React.MouseEvent<HTMLElement>) => {
    handleSubmit();
  };

  return (
    <WrapperButtonIcon isContent={disableSubmit} onClick={handleClick}>
      <LineIcon icon="ico-paperplane" />
    </WrapperButtonIcon>
  );
}

export default ButtonSubmitComposer;
