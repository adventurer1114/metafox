/**
 * @type: itemView
 * name: event.itemView.pendingReviewEventCard
 */
import { useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import {
  Box,
  Card,
  styled,
  Typography,
  IconButton,
  Stack,
  Button
} from '@mui/material';
import React from 'react';

const StyledCard = styled(Card)(({ theme }) => ({
  display: 'flex',
  justifyContent: 'space-between',
  background:
    theme.palette.mode === 'light'
      ? theme.palette.background.paper
      : theme.palette.grey['800'],
  padding: theme.spacing(2),
  color: theme.palette.text.secondary,
  boxShadow: 'none',
  marginTop: theme.spacing(2)
}));

const StyledTypography = styled(Typography)(({ theme }) => ({}));

const PreviewPendingCard = ({ item }) => {
  const { i18n, dispatch } = useGlobal();

  if (!item?.is_pending) return null;

  const { extra } = item;

  const handleApprove = () => {
    dispatch({ type: 'approveItem', payload: { identity: item._identity } });
  };

  const handleDecline = () => {
    dispatch({ type: 'deleteItem', payload: { identity: item._identity } });
  };

  return (
    <StyledCard>
      <Box sx={{ display: 'flex', alignItems: 'center' }}>
        <Box>
          <IconButton size={'medium'}>
            <LineIcon icon="ico-clock-o" />
          </IconButton>
        </Box>
        <Box sx={{ paddingLeft: 2 }}>
          <Typography variant="h5" color="text.primary" paddingBottom={0.5}>
            {i18n.formatMessage({ id: 'this_event_is_pending_state' })}
          </Typography>
          <StyledTypography variant="body2">
            {i18n.formatMessage({
              id: 'contents_from_this_event_will_be_public_visible_after_admins_approve_it'
            })}
          </StyledTypography>
        </Box>
      </Box>
      <Stack spacing={1} alignItems="center" direction="row">
        {extra?.can_approve ? (
          <>
            <Button size="small" variant="contained" onClick={handleApprove}>
              {i18n.formatMessage({ id: 'approve' })}
            </Button>
            <Button size="small" variant="outlined" onClick={handleDecline}>
              {i18n.formatMessage({ id: 'decline' })}
            </Button>
          </>
        ) : null}
      </Stack>
    </StyledCard>
  );
};

export default PreviewPendingCard;
