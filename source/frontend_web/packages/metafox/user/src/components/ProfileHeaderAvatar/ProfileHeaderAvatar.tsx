/**
 * @type: service
 * name: ProfileHeaderAvatar
 */
import { Link, useGlobal } from '@metafox/framework';
import { ProfileHeaderAvatarProps } from '@metafox/user';
import { LineIcon } from '@metafox/ui';
import { colorHash } from '@metafox/utils';
import { Avatar, Button, styled } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';

const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(({ theme }) => ({
  marginTop: -96,
  marginRight: theme.spacing(3),
  position: 'relative',
  [theme.breakpoints.down('sm')]: {
    marginTop: -64,
    marginRight: 0,
    marginBottom: theme.spacing(1)
  }
}));

const EditAvatarButton = styled(Button, { name: 'EditAvatarButton' })(
  ({ theme }) => ({
    textTransform: 'capitalize',
    position: 'absolute',
    top: theme.spacing(1),
    right: theme.spacing(1),
    minWidth: 32,
    height: 32,
    borderRadius: '100% !important',
    [theme.breakpoints.down('sm')]: {
      top: 0,
      right: 0
    }
  })
);

export default function ProfileHeaderAvatar(props: ProfileHeaderAvatarProps) {
  const {
    alt,
    avatar,
    avatarId,
    canEdit,
    onEdit,
    editLabel,
    editIcon = 'ico-camera'
  } = props;
  const classes = useStyles();
  const bgColor = colorHash.hex(alt || '');
  const to = `/photo/${avatarId}`;
  const avatarSize = {
    width: { sm: 168, xs: 92 },
    height: { sm: 168, xs: 92 },
    fontSize: { sm: 60, xs: 40 }
  };
  const { getSetting } = useGlobal();
  const appPhotoActive = getSetting('photo');

  return (
    <AvatarWrapper>
      {avatarId ? (
        <Link to={to} asModal>
          <Avatar
            component={'span'}
            alt={alt}
            children={alt}
            src={avatar}
            style={{ backgroundColor: bgColor }}
            className={classes.userAvatar}
            sx={avatarSize}
          />
        </Link>
      ) : (
        <Avatar
          component={'span'}
          alt={alt}
          children={alt}
          src={avatar}
          style={{ backgroundColor: bgColor }}
          className={classes.userAvatar}
          sx={avatarSize}
        />
      )}
      {canEdit && appPhotoActive ? (
        <EditAvatarButton
          aria-label={editLabel}
          onClick={onEdit}
          size="small"
          color="default"
        >
          <LineIcon icon={editIcon} className={classes.iconEdit} />
        </EditAvatarButton>
      ) : null}
    </AvatarWrapper>
  );
}
