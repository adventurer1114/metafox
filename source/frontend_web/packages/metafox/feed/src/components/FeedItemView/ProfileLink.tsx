import { RouteLink } from '@metafox/framework';
import { Box } from '@mui/material';
import * as React from 'react';

export default function ProfileLink({ user, className }) {
  if (!user) return null;

  if (user?.is_deleted) {
    return (
      <Box component="span" className={className}>
        {user?.full_name || user?.title}
      </Box>
    );
  }

  const to = user?.user_name
    ? `/${user?.user_name}`
    : `/${user?.resource_name}/${user?.id}`;

  const { resource_name, id } = user || {};

  return (
    <RouteLink
      to={to}
      className={className}
      hoverCard={`/${resource_name}/${id}`}
    >
      {user?.full_name || user?.title}
    </RouteLink>
  );
}
