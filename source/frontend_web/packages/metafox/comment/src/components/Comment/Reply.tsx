import { Link, useGlobal } from '@metafox/framework';
import { FromNow, UserAvatar } from '@metafox/ui';
import { Box, styled } from '@mui/material';
import React from 'react';
import { ReplyItemProps } from '../../types';
import Content from './Content';
import EditContent from './EditContent';

export type CommentItemState = {
  menuOpened: boolean;
  commentOpened: boolean;
};

const name = 'Reply';

const ItemOuter = styled('div', {
  name,
  slot: 'itemOuter',
  shouldForwardProp: prop => prop !== 'isLoading'
})<{ isLoading: boolean }>(({ theme, isLoading }) => ({
  display: 'flex',
  ...(isLoading && {
    opacity: 0.6,
    pointerEvents: 'none'
  }),
  '&:hover .itemActionMenu': {
    visibility: 'visible',
    marginLeft: theme.spacing(0.5)
  }
}));
const ItemInner = styled('div', { name, slot: 'itemInner' })(({ theme }) => ({
  display: 'flex',
  flexDirection: 'column',
  minWidth: 0,
  wordBreak: 'break-word'
}));
const ItemName = styled('div', { name, slot: 'itemName' })(({ theme }) => ({
  display: 'flex',
  fontSize: theme.mixins.pxToRem(13),
  marginBottom: theme.spacing(0.5)
}));
const AvatarWrapper = styled('div', { name, slot: 'avatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1)
  })
);
const UserName = styled(Link, { name, slot: 'userName' })(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(13),
  maxWidth: '100%',
  fontWeight: 'bold'
}));
const FromNowStyled = styled(FromNow, { name, slot: 'FromNow' })(
  ({ theme }) => ({
    display: 'flex',
    color: theme.palette.text.secondary,
    whiteSpace: 'nowrap',
    alignItems: 'flex-end',
    marginLeft: theme.spacing(1),
    '&:before': {
      display: 'none'
    }
  })
);
const Actions = styled('div', { name, slot: 'actions' })(({ theme }) => ({
  display: 'inline-flex',
  alignItems: 'center',
  height: theme.spacing(4),
  '&:empty': {
    height: theme.spacing(2)
  }
}));

const BoxWrapper = styled(Box, { name, slot: 'BoxMessageWrapper' })<{
  highlight?: boolean;
}>(({ theme, highlight }) => ({
  position: 'relative',
  '&:before': {
    content: '""',
    position: 'absolute',
    left: '-4px',
    top: '-4px',
    right: '-4px',
    bottom: '-4px',
    borderRadius: '8px',
    transition: 'background 300ms ease',
    background: 'none',
    pointerEvents: 'none',
    ...(highlight && {
      background:
        theme.palette.mode === 'dark'
          ? theme.palette.grey[600]
          : theme.palette.grey[100]
    })
  }
}));

export default function Reply({
  item,
  user,
  openReplyComposer,
  state,
  actions,
  extra_data,
  handleAction,
  identity,
  parent_user
}: ReplyItemProps) {
  const {
    ItemActionMenu,
    ReactionResult,
    ReactionActButton,
    ReplyActButton,
    RemovePreviewActButton,
    usePageParams,
    getSetting
  } = useGlobal();
  const isThreadDisplay = getSetting('comment.enable_thread');
  const { comment_id } = usePageParams();
  const [highlight, setHighlight] = React.useState(false);

  React.useEffect(() => {
    if (comment_id) {
      setHighlight(parseInt(comment_id) === item?.id);
      setTimeout(() => {
        setHighlight(false);
      }, 2000);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [comment_id]);

  if (!item) return null;

  const { text, editText, creation_date, most_reactions, statistic, link } =
    item;

  const showRemovePreviewButton =
    extra_data?.extra_type === 'link' && !item?.is_hide;

  return (
    <div>
      <Box pt={1}>
        {item.isEditing ? (
          <EditContent
            text={editText}
            extra_data={extra_data}
            handleAction={handleAction}
            identity={identity}
            isReply
            parent_user={parent_user}
            actions={actions}
          />
        ) : (
          <ItemOuter isLoading={item.isLoading}>
            <AvatarWrapper>
              <UserAvatar user={user} size={32} />
            </AvatarWrapper>
            <ItemInner>
              <BoxWrapper p={0} borderRadius={2} highlight={highlight}>
                <Box sx={{ position: 'relative', zIndex: 2 }}>
                  <ItemName className={'dotSeparators'}>
                    <UserName
                      hoverCard
                      to={`/user/${user.id}`}
                      children={user.full_name}
                    />
                    <Link color="inherit" to={link}>
                      <FromNowStyled value={creation_date} shorten />
                    </Link>
                  </ItemName>
                  <Content
                    text={text}
                    extra_data={!item?.hideExtraData ? extra_data : null}
                  />
                </Box>
              </BoxWrapper>
              <Actions className={'dotSeparators'}>
                {ReactionActButton && item?.extra?.can_like ? (
                  <ReactionActButton
                    minimize
                    reacted={item.user_reacted}
                    identity={identity}
                    handleAction={handleAction}
                  />
                ) : null}
                {ReplyActButton &&
                isThreadDisplay &&
                item?.extra?.can_comment ? (
                  <ReplyActButton
                    minimize
                    identity={identity}
                    openReplyComposer={openReplyComposer}
                    handleAction={handleAction}
                  />
                ) : null}
                {showRemovePreviewButton ? (
                  <RemovePreviewActButton
                    actions={actions}
                    identity={identity}
                    minimize
                  />
                ) : null}
                <ReactionResult
                  size="sm"
                  identity={identity}
                  handleAction={handleAction}
                  data={most_reactions}
                  total={statistic?.total_like}
                />
                <ItemActionMenu
                  identity={identity}
                  state={state}
                  handleAction={handleAction}
                  size="smaller"
                  sx={{ visibility: 'hidden' }}
                  className={'itemActionMenu'}
                />
              </Actions>
            </ItemInner>
          </ItemOuter>
        )}
      </Box>
    </div>
  );
}
