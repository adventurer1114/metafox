/**
 * @type: ui
 * name: dataGrid.cell.HtmlCell
 */
import React from 'react';
import HtmlViewer from '@metafox/html-viewer';
import { Box } from '@mui/material';
import { get } from 'lodash';

export default function HtmlCell({ row, colDef: { field } }) {
  const content = get(row, field, null);
  const sx = get(row, 'sx');
  const sxProps = get(sx, field);

  return (
    <Box component={'span'} sx={sxProps}>
      <HtmlViewer html={content || ''} />
    </Box>
  );
}
