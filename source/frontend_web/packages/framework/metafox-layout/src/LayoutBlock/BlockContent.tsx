/**
 * @type: ui
 * name: ui.block.default.content
 */
import { useBlock } from '@metafox/layout';
import { Box } from '@mui/material';
import { styled } from '@mui/material/styles';
import * as React from 'react';

interface Props {
  children?: React.ReactNode;
  style?: React.CSSProperties;
}

export type BlockWrapperProps = {
  children?: any;
  testid?: string;
  blockProps: any;
};

const BlockContent = styled(Box, {
  name: 'MuiBlock',
  slot: 'content',
  shouldForwardProp: (prop: string) =>
    prop !== 'maxWidth' && prop !== 'fullHeight' && prop !== 'variant'
})({
  position: 'relative',
  boxSizing: 'border-box'
});

export default function BlockContentRoot(props: Props) {
  const { blockProps: { contentStyle } = {} } = useBlock();

  return <BlockContent {...contentStyle} {...props} />;
}
