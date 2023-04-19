import { useGlobal, useResourceMenu } from '@metafox/framework';
import {
  ItemActionMenu,
  ItemMedia,
  ItemText,
  ItemTitle,
  LineIcon,
  ItemView,
  UserAvatar
} from '@metafox/ui';
import { styled, Typography } from '@mui/material';
import React from 'react';

const TypographyStyled = styled(Typography)(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(13),
  color: theme.palette.grey[500],
  display: 'inline-flex',
  alignItems: 'center',
  cursor: 'pointer',
  '& .ico': {
    paddingLeft: theme.spacing(0.5)
  }
}));

export default function ShortcutItem({
  item,
  identity,
  wrapAs,
  handleAction,
  wrapProps
}) {
  const menu = useResourceMenu('user', 'shortcut', 'itemActionMenu');
  const { i18n } = useGlobal();

  if (!item) return null;

  const itemAvatar = {
    full_name: item.full_name,
    avatar: item.avatar
  };

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
      style={{ overflow: 'visible' }}
    >
      <ItemMedia>
        <UserAvatar
          user={itemAvatar}
          size={32}
          variant={item?.module_name === 'group' ? 'rounded' : 'circular'}
        />
      </ItemMedia>
      <ItemText>
        <ItemTitle>{item.full_name}</ItemTitle>
        <ItemActionMenu
          disablePortal
          items={menu.items}
          identity={identity}
          handleAction={handleAction}
          placement="bottom-start"
          control={
            <TypographyStyled variant="body1">
              {i18n.formatMessage({ id: menu.items[item.sort_type].label })}
              <LineIcon icon="ico-caret-down" />
            </TypographyStyled>
          }
        />
      </ItemText>
    </ItemView>
  );
}
