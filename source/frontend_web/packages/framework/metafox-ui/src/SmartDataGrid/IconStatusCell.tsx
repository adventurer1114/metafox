/**
 * @type: ui
 * name: dataGrid.cell.IconStatusCell
 */
import React from 'react';
import { Box, styled, Tooltip } from '@mui/material';
import { get } from 'lodash';
import { LineIcon } from '@metafox/ui';
import { keyframes } from '@emotion/react';

const spinnerKeyFrame = keyframes`
    0% {transform: rotate(0deg);}
    100% {transform: rotate(360deg);}
`;

const name = 'IconCell';
const IconWrapper = styled('div', {
  name,
  slot: 'uiChatMsgSet',
  shouldForwardProp: prop => prop !== 'isOwner'
})<{ spinner?: boolean }>(({ theme, spinner }) => ({
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  ...(spinner && { animation: `${spinnerKeyFrame} 1s linear infinite` })
}));

export default function IconCell({
  row,
  colDef: { field, iconConfig, iconDefault }
}) {
  const content = get(row, field, null);
  const key = content?.toString();

  if (key === '') return null;

  const {
    icon,
    color,
    spinner,
    label,
    hidden = false,
    asText
  } = iconConfig[key] || iconDefault || {};
  const sx = get(row, 'sx');
  const sxProps = get(sx, field);

  if (hidden) return null;

  if (!icon)
    return (
      <Box component={'span'} sx={sxProps}>
        {key}
      </Box>
    );

  return (
    <Box component={'span'} color={color} sx={sxProps}>
      <Tooltip title={label || key}>
        {asText ? (
          <Box component={'span'}>{asText}</Box>
        ) : (
          <IconWrapper spinner={spinner}>
            <LineIcon sx={{ fontSize: '18px' }} icon={icon} />
          </IconWrapper>
        )}
      </Tooltip>
    </Box>
  );
}
