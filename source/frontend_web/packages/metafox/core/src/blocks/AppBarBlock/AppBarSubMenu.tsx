import { useAppMenu, useGlobal, HistoryState } from '@metafox/framework';
import { MenuItems } from '@metafox/ui';
import { filterShowWhen } from '@metafox/utils';
import { Box } from '@mui/material';
import React from 'react';
import { useLocation } from 'react-router-dom';
import useStyles from './AppBar.styles';

export default function AppBarSubMenu() {
  const { useActionControl, getAcl, useSession, getSetting } = useGlobal();
  const session = useSession();
  const acl = getAcl();
  const setting = getSetting();
  const { pathname: _pathname, state } = useLocation<HistoryState>();
  const [handleAction] = useActionControl<unknown, unknown>(null, {});
  const subMenu = useAppMenu('core', 'headerSubMenu');
  const classes = useStyles();
  const pathname = state?.as || _pathname;

  if (!subMenu || !subMenu.items) return null;

  const filteredItems = filterShowWhen(subMenu.items, {
    acl,
    setting,
    session
  });

  return (
    <Box
      sx={{ display: 'flex', alignItems: 'center', marginLeft: 'auto' }}
      data-testid="menuAppBar"
    >
      <MenuItems
        prefixName="appbar.item."
        fallbackName="popover"
        items={filteredItems}
        handleAction={handleAction}
        classes={classes}
        pathname={pathname}
      />
    </Box>
  );
}
