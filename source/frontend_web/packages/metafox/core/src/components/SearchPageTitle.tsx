/**
 * @type: ui
 * name: SearchPageTitle
 */
import { useGetItem, useGlobal } from '@metafox/framework';
import React from 'react';
import { Box } from '@mui/material';

export default function SearchPageTitle({
  identity,
  alt = null,
  message,
  styleProps
}) {
  const { i18n } = useGlobal();
  const category = useGetItem(identity);

  if (!category) return alt;

  if (message) {
    return (
      <Box {...styleProps}>
        {i18n.formatMessage(
          { id: message },
          {
            title: category.title ?? category.name ?? alt
          }
        )}
      </Box>
    );
  }

  return category.title ?? category.name ?? alt;
}
