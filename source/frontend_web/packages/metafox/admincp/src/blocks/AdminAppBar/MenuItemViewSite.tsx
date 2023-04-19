/**
 * @type: ui
 * name: appbar.item.viewSite
 */
import { LineIcon, MenuItemViewProps as Props } from '@metafox/ui';
import { Tooltip } from '@mui/material';
import React from 'react';

export default function AsDivider({ item, classes }: Props) {
  return (
    <a
      className={classes.menuItemViewSite}
      href="/"
      target="_blank"
      data-testid={item.testid || item.name}
    >
      <span className={classes.smallMenuButton}>
        <Tooltip title={item?.label || ''}>
          <LineIcon icon={item.icon} component="i" />
        </Tooltip>
      </span>
    </a>
  );
}
