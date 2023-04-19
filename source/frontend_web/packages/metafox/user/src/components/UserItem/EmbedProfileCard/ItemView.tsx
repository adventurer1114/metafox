import { FeedEmbedCardProps, LineIcon } from '@metafox/ui';
import { UserItemShape } from '@metafox/user';
import { Box, Typography } from '@mui/material';
import { styled } from '@mui/material/styles';
import React from 'react';

const Item = styled(Box, { name: 'ItemInner' })(({ theme }) => ({
  textAlign: 'center',
  display: 'flex',
  borderRadius: 8,
  border: theme.mixins.border('secondary'),
  backgroundColor: theme.mixins.backgroundColor('paper'),
  overflow: 'hidden',
  padding: theme.spacing(1.5, 3),
  alignItems: 'center',
  justifyContent: 'center'
}));

const IconRelation = styled(Box, { name: 'IconRelation' })(({ theme }) => ({
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  width: theme.spacing(6),
  height: theme.spacing(6),
  borderRadius: '100%',
  background: theme.palette.background.default,
  color: theme.palette.grey[600],
  fontSize: '24px'
}));

type RalationType = { label: string; value: number };
type UserProfileItemShape = {
  user: UserItemShape;
  relation: RalationType;
  relation_with?: string;
};
type EmbedUserProfileItemProps = FeedEmbedCardProps & {
  item: UserProfileItemShape;
};

export default function EmbedUserProfileItem({
  item,
  variant
}: EmbedUserProfileItemProps) {
  const { relation } = item || {};

  if (!item) return null;

  return (
    <Item>
      {relation ? (
        <Box>
          <IconRelation>
            <LineIcon icon="ico-user-couple" />
          </IconRelation>
          <Typography mt={1.5} color="text.secondary" variant="body1">
            {relation.label}
          </Typography>
        </Box>
      ) : null}
    </Item>
  );
}
