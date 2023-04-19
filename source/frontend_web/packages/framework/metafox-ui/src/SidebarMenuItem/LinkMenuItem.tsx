/**
 * @type: ui
 * name: menuItem.as.sidebarLink
 */
import { RouteLink } from '@metafox/framework';
import { ControlMenuItemProps, LineIcon } from '@metafox/ui';
import clsx from 'clsx';
import React from 'react';
import { MenuItem, ListItemIcon, ListItemText } from '@mui/material';

export default function SidebarLinkMenuItem({
  item,
  active,
  classes,
  variant
}: ControlMenuItemProps) {
  const { label, to, icon, onClick, testid } = item;

  return (
    <MenuItem<any>
      role="menuitem"
      className={clsx(classes.menuItem, active && classes.activeMenuItem)}
      component={RouteLink}
      to={to}
      onClick={onClick}
      selected={active}
      data-testid={testid || label || icon}
      variant={variant}
    >
      {icon ? (
        <ListItemIcon>
          <LineIcon icon={icon} />
        </ListItemIcon>
      ) : null}
      <ListItemText className={classes.menuItemText} primary={label} />
    </MenuItem>
  );
}
