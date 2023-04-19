/**
 * @type: ui
 * name: adminSideMenu.as.normal
 */
import { RouteLink } from '@metafox/framework';
import { LineIcon, MenuItemViewProps } from '@metafox/ui';
import React from 'react';
import clsx from 'clsx';

export default function MenuItem({
  item,
  classes,
  handleAction,
  pathname,
  active,
  minimize,
  activeHover,
  handlePopoverClose,
  handlePopoverOpen
}: MenuItemViewProps) {
  const openTypeMinimize = minimize && activeHover;

  if (item.value) {
    return (
      <div
        className={clsx(classes.menuItem, {
          [classes.menuItemActive]: active,
          [classes.menuItemMinimize]: minimize,
          [classes.menuItemMinimizeOpen]: openTypeMinimize
        })}
        onMouseEnter={handlePopoverOpen}
        onMouseLeave={handlePopoverClose}
      >
        <div
          className={classes.menuItemLink}
          onClick={() => handleAction(item.value)}
        >
          <LineIcon icon={item.icon} className={classes.menuItemIcon} />
          {!minimize ? <span>{item.label}</span> : null}
        </div>
      </div>
    );
  }

  return (
    <div
      className={clsx(classes.menuItem, {
        [classes.menuItemActive]: active,
        [classes.menuItemMinimize]: minimize,
        [classes.menuItemMinimizeOpen]: openTypeMinimize
      })}
      onMouseEnter={handlePopoverOpen}
      onMouseLeave={handlePopoverClose}
    >
      <RouteLink
        className={classes.menuItemLink}
        to={item.to}
        data-testid={item.testid || item.name}
        onClick={item.value ? () => handleAction(item.value) : undefined}
      >
        <LineIcon icon={item.icon} className={classes.menuItemIcon} />
        {!minimize ? <span>{item.label}</span> : null}
      </RouteLink>
    </div>
  );
}
