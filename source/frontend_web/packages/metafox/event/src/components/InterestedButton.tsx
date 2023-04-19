import { HandleAction, useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { IconButton, styled, Typography } from '@mui/material';
import React from 'react';
import { mappingRSVP } from '../utils';

const IconButtonAction = styled(IconButton)(({ theme }) => ({
  ...(theme.palette.mode === 'dark' && {
    '&:hover': {
      backgroundColor: theme.palette.grey[700]
    }
  })
}));

const TypographyStyled = styled(Typography)(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15),
  fontWeight: '600'
}));

const OwnerStyled = styled(Typography)(({ theme }) => ({
  backgroundColor: theme.palette.background.default,
  borderRadius: 4,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  height: '100%',
  padding: `0 ${theme.spacing(2)}`,
  minHeight: '40px'
}));

interface InterestedButtonProp {
  handleAction: HandleAction;
  identity: string;
  rsvp: number;
  disabled: boolean;
  menus?: Array<Record<string, any>>;
}

export default function InterestedButton({
  handleAction,
  identity,
  rsvp,
  disabled,
  menus
}: InterestedButtonProp) {
  const { i18n, ItemActionMenu, useGetItem, useSession } = useGlobal();
  const item = useGetItem(identity);
  const user = useGetItem(item?.user);
  const { user: authUser } = useSession();
  const isOwner = authUser?.id === user?.id;
  const { icon, label } = mappingRSVP(rsvp);

  if (isOwner) {
    return (
      <OwnerStyled>
        <Typography variant={'body1'} fontWeight={600} color="text.hint">
          {i18n.formatMessage({ id: 'your_event' })}
        </Typography>
      </OwnerStyled>
    );
  }

  return (
    <ItemActionMenu
      sx={{ width: '100%', height: '100%' }}
      label={i18n.formatMessage({ id: label })}
      id="interested"
      items={menus}
      handleAction={handleAction}
      menuName={'interestedMenu'}
      identity={identity}
      control={
        <IconButtonAction
          disabled={disabled}
          color="primary"
          variant="outlined-square"
          size="medium"
        >
          <LineIcon icon={icon} sx={{ mr: 1, fontSize: 13 }} />
          <TypographyStyled variant="body1">
            {i18n.formatMessage({ id: label })}
          </TypographyStyled>
          <LineIcon icon={'ico-caret-down'} sx={{ ml: 1, fontSize: 18 }} />
        </IconButtonAction>
      }
    />
  );
}
