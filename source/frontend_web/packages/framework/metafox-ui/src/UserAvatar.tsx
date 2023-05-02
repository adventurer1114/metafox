import { RouteLink, useGlobal } from '@metafox/framework';
import { colorHash, getImageSrc, shortenFullName } from '@metafox/utils';
import { Avatar, AvatarProps, useTheme, styled } from '@mui/material';
import { isEmpty } from 'lodash';
import React from 'react';

interface Base {
  full_name?: string;
  title?: string;
  link?: string;
  short_name?: string;
  avatar?: any;
  cover?: any;
}
export interface UserAvatarProps<T extends Base = Base> extends AvatarProps {
  user: T;
  size?: number;
  to?: string;
  onClick?: any;
  noLink?: boolean;
  component?: string;
  'data-testid'?: string;
  hoverCard?: any;
  cover?: boolean;
  srcSizePrefers?: string;
}

const useMappingDefaultPhoto = user => {
  const { assetUrl } = useGlobal();

  if (isEmpty(user)) return;

  let source;

  const { module_name } = user;

  switch (module_name) {
    case 'group':
      source = assetUrl('group.cover_no_image');
      break;
    default:
      source = undefined;
  }

  return source;
};

const AvatarWrapper = styled(Avatar, { name: 'AvatarWrapper' })(
  ({ theme }) => ({
    borderWidth: 'thin',
    borderStyle: 'solid',
    borderColor: theme.palette.border.secondary
  })
);

export default function UserAvatar({
  user,
  size = 24,
  to,
  item = {},
  noLink,
  onClick,
  hoverCard: hoverCardProp,
  src,
  'data-testid': testid = 'userAvatar',
  cover,
  srcSizePrefers = '200x200',
  ...rest
}: UserAvatarProps) {
  const theme = useTheme();
  const altSrc = useMappingDefaultPhoto(user);
  const title = user?.title ?? (user?.full_name || 'NaN');
  const alt = shortenFullName(user?.full_name || user?.title);
  const style: any = {
    width: size,
    height: size,
    color: theme.palette.grey['50'],
    fontSize: size / 3
  };
  const hoverCardUrl =
    user?.module_name === 'user' && user?.id ? `/user/${user?.id}` : '';
  const hoverCard = hoverCardProp ?? hoverCardUrl;
  const avatar =
    src ||
    getImageSrc(!cover ? user?.avatar : user?.cover, srcSizePrefers, altSrc);

  if (!avatar) {
    style.backgroundColor = colorHash.hex(alt || '');
  }

  if (onClick || noLink) {
    return (
      <AvatarWrapper
        src={avatar}
        data-testid={testid}
        alt={title}
        style={style}
        component={'span'}
        role="button"
        onClick={onClick}
        children={alt}
        {...rest}
      />
    );
  }

  return (
    <AvatarWrapper
      src={avatar}
      alt={title}
      style={style}
      component={RouteLink}
      to={to ?? user?.link}
      data-testid={testid}
      hoverCard={hoverCard}
      children={alt}
      draggable={false}
      variant={rest?.variant}
      {...rest}
    />
  );
}
