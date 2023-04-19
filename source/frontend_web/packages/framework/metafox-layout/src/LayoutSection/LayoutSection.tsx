/**
 * @type: service
 * name: LayoutSection
 * chunkName: boot
 */

import { useGlobal } from '@metafox/framework';
import { Box, styled } from '@mui/material';
import { EditMode } from '../types';
import React from 'react';

export type LayoutSectionProps = {
  sectionName: 'content' | 'header' | 'footer';
  layoutEditMode: EditMode;
  elements?: any[];
};

const Root = styled(Box, {
  name: 'LayoutSection',
  slot: 'EditingRoot',
  shouldForwardProp: prop => prop !== 'sectionName'
})<{ sectionName: string }>(({ sectionName }) => ({
  backgroundRepeat: 'repeat',
  backgroundImage: `url(https://metafox-dev.s3.amazonaws.com/kl/bg-${sectionName}.png)`,
  ...(sectionName === 'content' && {
    backgroundColor: '#e9e9e9',
    minHeight: 300
  }),
  ...(sectionName === 'header' && {
    backgroundColor: '#ededed',
    minHeight: 180
  }),
  ...(sectionName === 'footer' && {
    backgroundColor: '#ededed',
    minHeight: 180
  })
}));

export default function LayoutSection({
  elements,
  sectionName,
  layoutEditMode
}: LayoutSectionProps) {
  const { jsxBackend } = useGlobal();

  if (!elements?.length) {
    return null;
  }

  if (
    layoutEditMode === EditMode.editLayout ||
    layoutEditMode === EditMode.editPageContent
  ) {
    return <Root sectionName={sectionName}>{jsxBackend.render(elements)}</Root>;
  }

  return jsxBackend.render(elements);
}
