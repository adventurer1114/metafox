/**
 * @type: ui
 * name: chatplusToolbar.item.button
 */
import { LineIcon } from '@metafox/ui';
import { Tooltip } from '@mui/material';
import clsx from 'clsx';
import React from 'react';

export default function ToolbarItem({
  item: { value, icon, active, label },
  handleAction,
  classes
}) {
  const onClick = (evt: React.SyntheticEvent<HTMLButtonElement>) => {
    if (evt) {
      evt.stopPropagation();
    }

    if (value) handleAction(value);
  };

  return (
    <div className={clsx(classes.item, active && classes.itemActive)}>
      <Tooltip title={label ?? ''} placement="top">
        <button
          className={clsx(classes.btn, active && classes.btnActive)}
          onClick={onClick}
        >
          {icon ? <LineIcon icon={icon} /> : null}
        </button>
      </Tooltip>
    </div>
  );
}
