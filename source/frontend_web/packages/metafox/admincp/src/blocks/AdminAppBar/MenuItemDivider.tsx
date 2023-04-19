/**
 * @type: ui
 * name: appbar.item.divider
 */
import { MenuItemViewProps as Props } from '@metafox/ui';
import React from 'react';

export default function AsDivider({ classes }: Props) {
  return <div className={classes.divider} />;
}
