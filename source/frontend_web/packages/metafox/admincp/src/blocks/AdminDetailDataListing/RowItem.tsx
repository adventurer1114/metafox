import { useGlobal } from '@metafox/framework';
import { UIBlockViewProps } from '@metafox/ui';
import { Box, Typography } from '@mui/material';
import React from 'react';

export interface Props extends UIBlockViewProps {}

export default function Item({ data }) {
  const { jsxBackend } = useGlobal();
  const { label, type, value } = data || {};
  const name = `dataListing.value.${type || 'default'}`;

  if (!value) return null;

  return (
    <Box mb={1}>
      <Box sx={{ display: 'flex' }}>
        <Box mr={1}>
          <Typography variant="body1" fontWeight={600}>
            {label}:
          </Typography>
        </Box>
        <Typography variant="body1">
          {jsxBackend.render({
            component: name,
            props: {
              ...data
            }
          })}
        </Typography>
      </Box>
    </Box>
  );
}
