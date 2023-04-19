/**
 * @type: ui
 * name: layout.containerWithEditingPage
 */
import { useGlobal } from '@metafox/framework';
import { Box } from '@mui/material';
import Grid from '@mui/material/Grid';
import React from 'react';
import { LayoutContainerProps } from '../types';
import Container from './Container';

export default function ContainerWithEditingPage({
  layoutEditMode,
  elements,
  master,
  rootStyle
}: LayoutContainerProps & { children: React.ReactNode }) {
  // how to edit section with item
  const { jsxBackend } = useGlobal();

  return (
    <Box sx={{ maxWidth: 1280, marginLeft: 'auto', marginRight: 'auto' }}>
      <Container master={master} editMode={layoutEditMode} {...rootStyle}>
        <Grid container columnSpacing={0} rowSpacing={0}>
          {jsxBackend.render(elements)}
        </Grid>
      </Container>
    </Box>
  );
}
