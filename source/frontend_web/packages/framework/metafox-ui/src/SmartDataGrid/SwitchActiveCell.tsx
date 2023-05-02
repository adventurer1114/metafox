/**
 * @type: ui
 * name: dataGrid.cell.SwitchActiveCell
 */
import { useGlobal } from '@metafox/framework';
import { Box, Switch, Tooltip } from '@mui/material';
import { get } from 'lodash';
import React from 'react';
import useDataGridContext from './useDataGridContext';

// todo moved this column to base size.
export default function SwitchActiveCell({
  id,
  row,
  colDef: { field, reload, action = 'toggleActive' }
}) {
  const { handleRowAction } = useDataGridContext();
  const { i18n } = useGlobal();
  const value = get(row, field);

  return (
    <Box
      sx={{
        display: 'flex',
        height: '100%',
        alignItems: 'center'
      }}
    >
      <Tooltip
        title={i18n.formatMessage({
          id: row[field] ? 'deactivate' : 'activate'
        })}
        placement="top"
      >
        <Box sx={{ display: 'inline-flex', width: 33 }}>
          <Switch
            size="small"
            disabled={value === null || row._dirty}
            checked={value === null || Boolean(value)}
            onChange={() =>
              handleRowAction('row/active', {
                action,
                field,
                id,
                row,
                reload
              })
            }
          />
        </Box>
      </Tooltip>
    </Box>
  );
}
