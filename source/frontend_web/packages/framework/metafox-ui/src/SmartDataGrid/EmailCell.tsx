/**
 * @type: ui
 * name: dataGrid.cell.EmailCell
 */

import { Box } from '@mui/material';
import { get } from 'lodash';
import React from 'react';

const MAIL_TO = 'mailto:';

export default function EmailCell({ row, colDef: { field, mailto } }) {
  const content = get(row, field, null);
  const sx = get(row, 'sx');
  const sxProps = get(sx, field);
  const mailUrl = get<string>(row, mailto);
  const url = mailUrl ? `${MAIL_TO}${mailUrl}` : '';

  return (
    <Box component={'span'} sx={sxProps}>
      {url ? (
        <a href={url} rel="noopener noreferrer">
          {content}
        </a>
      ) : (
        content
      )}
    </Box>
  );
}
