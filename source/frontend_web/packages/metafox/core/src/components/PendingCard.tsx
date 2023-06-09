/**
 * @type: itemView
 * name: core.itemView.pendingReviewCard
 */
import { useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import {
  Box,
  Card,
  IconButton,
  styled,
  Typography,
  Stack,
  Button
} from '@mui/material';
import React from 'react';

const StyledCard = styled(Card)(({ theme }) => ({
  display: 'flex',
  justifyContent: 'space-between',
  background:
    theme.palette.mode === 'light'
      ? theme.layoutSlot.background.paper
      : theme.palette.background.default,
  padding: theme.spacing(2),
  color: theme.palette.text.secondary,
  boxShadow: 'none'
}));

const StyledTypography = styled(Typography)(({ theme }) => ({}));

const PreviewPendingCard = ({ item, sx, sxWrapper }) => {
  const { i18n, dispatch } = useGlobal();

  if (!item?.is_pending) return null;

  const { extra, resource_name } = item;

  const handleApprove = () => {
    dispatch({ type: 'approveItem', payload: { identity: item._identity } });
  };

  const handleDecline = () => {
    dispatch({ type: 'deleteItem', payload: { identity: item._identity } });
  };

  return (
    <Box sx={sxWrapper}>
      <StyledCard sx={sx}>
        <Box sx={{ display: 'flex', alignItems: 'center' }}>
          <Box>
            <IconButton size={'medium'}>
              <LineIcon icon="ico-clock-o" />
            </IconButton>
          </Box>
          <Box sx={{ paddingLeft: 2 }}>
            <Typography variant="h5" color="text.primary" paddingBottom={0.5}>
              {i18n.formatMessage(
                { id: 'this_app_is_pending_state' },
                {
                  value: i18n.formatMessage({
                    id: `resource_name_lower_case_${resource_name}`,
                    defaultMessage: resource_name
                  })
                }
              )}
            </Typography>
            <StyledTypography variant="body2">
              {i18n.formatMessage(
                {
                  id: 'contents_from_this_app_will_be_public_visible_after_admins_approve_it'
                },
                {
                  value: i18n.formatMessage({
                    id: `resource_name_lower_case_${resource_name}`,
                    defaultMessage: resource_name
                  })
                }
              )}
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
    </Box>
  );
};

export default PreviewPendingCard;
