import { Link, useGlobal } from '@metafox/framework';
import { ManageHiddenItemProps } from '@metafox/feed/types';
import {
  ItemAction,
  ItemMedia,
  ItemText,
  ItemTitle,
  ItemView,
  UserAvatar
} from '@metafox/ui';
import { Button } from '@mui/material';
import { styled } from '@mui/material/styles';
import React from 'react';
import LoadingSkeleton from './LoadingSkeleton';

const UnHideButton = styled(Button)(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(13),
  fontWeight: 'bold'
}));
const UndoButton = styled(Button)(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(13),
  fontWeight: 'bold',
  color: theme.palette.text.primary,
  borderColor: theme.palette.border?.secondary,
  '&:hover': {
    backgroundColor: theme.palette.action.hover
  }
}));

export default function FeedHiddenItemView({
  item,
  user,
  actions,
  identity,
  itemProps,
  wrapAs,
  wrapProps
}: ManageHiddenItemProps) {
  const { i18n, dispatch } = useGlobal();

  const handleUnhideButton = () => {
    dispatch({
      type: 'unhideItem',
      payload: { identity }
    });
  };

  const handleUndoButton = () => {
    dispatch({
      type: 'undoUnhideItem',
      payload: { identity }
    });
  };

  if (!item) return null;

  const { unHide, link: to } = item;

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <ItemMedia>
        <UserAvatar user={user} size={32} />
      </ItemMedia>
      <ItemText>
        <ItemTitle>
          <Link to={to} children={user.full_name} color={'inherit'} />
        </ItemTitle>
      </ItemText>
      <ItemAction>
        {!unHide ? (
          <UnHideButton
            size="small"
            variant="outlined"
            color="primary"
            onClick={handleUnhideButton}
          >
            {i18n.formatMessage({ id: 'unhide' })}
          </UnHideButton>
        ) : (
          <UndoButton
            size="small"
            variant="outlined"
            color="default"
            onClick={handleUndoButton}
          >
            {i18n.formatMessage({ id: 'undo' })}
          </UndoButton>
        )}
      </ItemAction>
    </ItemView>
  );
}

FeedHiddenItemView.LoadingSkeleton = LoadingSkeleton;
