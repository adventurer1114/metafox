/**
 * @type: ui
 * name: ui.block.default
 * chunkName: boot
 */
import { useBlock } from '@metafox/layout';
import { Box } from '@mui/material';
import { styled } from '@mui/material/styles';
import React from 'react';

type Props = {
  children?: any;
  testid?: string;
};

export type BlockWrapperProps = {
  children?: any;
  testid?: string;
  blockProps: any;
};

const MuiBlock = styled(Box, {
  name: 'MuiBlock',
  slot: 'Root',
  shouldForwardProp: (prop: string) =>
    prop !== 'maxWidth' &&
    prop !== 'fullHeight' &&
    prop !== 'variant' &&
    prop !== 'dividerVariant' &&
    prop !== 'bgColor' &&
    prop !== 'sx'
})<{ sx?: any }>(({ theme, sx }) => ({
  position: 'relative',
  ...(sx?.maxWidth &&
    ['xs1', 'xs2', 'xs3', 'sm1'].includes(sx?.maxWidth) && {
      maxWidth: `${theme.layoutSlot.points[sx?.maxWidth]}px !important`
    })
}));

export default function BlockContainer({ children, testid }: Props) {
  const { blockProps: { blockStyle } = {} } = useBlock();

  if (!children) return null;

  return (
    <MuiBlock {...blockStyle} data-testid={testid}>
      {children}
    </MuiBlock>
  );
}
