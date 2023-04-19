/**
 * @type: ui
 * name: chatplusToolbar.item.dropdown
 */
import { ItemActionMenu, LineIcon } from '@metafox/ui';
import { styled, Tooltip } from '@mui/material';
import clsx from 'clsx';
import React from 'react';

const ItemActionMenuStyled = styled(ItemActionMenu, {
  shouldForwardProp: props => props !== 'variant'
})<{ variant?: string }>(({ theme, variant }) => ({
  ...(variant === 'pageMessage' && {
    width: '190px'
  })
}));

const ButtonStyled = styled('button', {
  shouldForwardProp: props => props !== 'variant'
})<{ variant?: string }>(({ theme, variant }) => ({
  height: '34px',
  lineHeight: '20px',
  fontSize: '17px',
  padding: '0 6px',
  cursor: 'pointer',
  color: theme.palette.text.hint,
  position: 'relative',
  minWidth: theme.spacing(3),
  display: 'inline-flex',
  textAlign: 'center',
  alignItems: 'center',
  justifyContent: 'center',
  border: 'none',
  outline: 'none',
  backgroundColor: 'transparent',
  '&:hover': {
    color: theme.palette.text.secondary
  },
  ...(variant === 'pageMessage' && {
    color:
      theme.palette.mode === 'light'
        ? theme.palette.grey['600']
        : theme.palette.text.primary,
    fontSize: theme.spacing(2.25),
    minWidth: theme.spacing(6.25)
  })
}));

export default function ToolbarItem({
  item: { icon, active, label, items, disablePortal = true },
  handleAction,
  classes,
  variant
}) {
  return (
    <ItemActionMenuStyled
      placement="bottom-end"
      items={items}
      disablePortal={disablePortal}
      handleAction={handleAction}
      variant={variant}
      control={
        <div className={clsx(classes.item, active && classes.itemActive)}>
          <Tooltip title={label ?? ''} placement="top">
            <ButtonStyled
              variant={variant}
              className={clsx(classes.btn, active && classes.btnActive)}
            >
              {icon ? <LineIcon icon={icon} /> : null}
            </ButtonStyled>
          </Tooltip>
        </div>
      }
    />
  );
}
