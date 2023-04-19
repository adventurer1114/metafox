import { RefOf } from '@metafox/framework';
import { CommentComposerPluginControlProps } from '@metafox/comment/types';
import { LineIcon } from '@metafox/ui';
import { IconButton, styled, Tooltip } from '@mui/material';
import React from 'react';

const IconButtonStyled = styled(IconButton)(({ theme }) => ({
  padding: 0,
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  width: theme.spacing(3.5),
  height: theme.spacing(3.5),
  minWidth: theme.spacing(3.5),
  color: theme.palette.text.secondary
}));
const LineIconStyled = styled(LineIcon)(({ theme }) => ({
  fontSize: theme.spacing(1.75)
}));

function ChatComposerControl(
  { title, icon, onClick, testid }: CommentComposerPluginControlProps,
  ref: RefOf<HTMLButtonElement>
) {
  return (
    <Tooltip title={title}>
      <IconButtonStyled
        onClick={onClick}
        size="small"
        ref={ref}
        data-testid={testid}
        role="button"
      >
        <LineIconStyled icon={icon} />
      </IconButtonStyled>
    </Tooltip>
  );
}

export default React.forwardRef<
  HTMLButtonElement,
  CommentComposerPluginControlProps
>(ChatComposerControl);
