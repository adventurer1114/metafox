import { Link, useGlobal } from '@metafox/framework';
import { FromNow, LineIcon, UserAvatar, ActButton } from '@metafox/ui';
import { Box, styled, CircularProgress, Tooltip } from '@mui/material';
import React from 'react';
import { CommentItemProps } from '../../types';
import Content from './Content';
import EditContent from './EditContent';
import PreFetchComment from './PreFetchComment';

const name = 'Comment';

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
const AvatarWrapper = styled('div', { name, slot: 'AvatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1)
  })
);
const ItemName = styled('div', { name, slot: 'ItemName' })(({ theme }) => ({
  display: 'flex',
  fontSize: theme.mixins.pxToRem(13),
  marginBottom: theme.spacing(0.5)
}));
const UserNameStyled = styled(Link, { name, slot: 'userName' })(
  ({ theme }) => ({
    fontSize: theme.mixins.pxToRem(13),
    maxWidth: '100%',
    fontWeight: 'bold'
  })
);
const FromNowStyled = styled(FromNow, { name, slot: 'FromNowStyled' })(
  ({ theme }) => ({
    display: 'flex',
    color: theme.palette.text.secondary,
    whiteSpace: 'nowrap',
    alignItems: 'flex-end',
    marginLeft: theme.spacing(1)
  })
);
const ReactionsWrapper = styled('div', { name, slot: 'ReactionsWrapper' })(
  ({ theme }) => ({
    display: 'inline-flex',
    alignItems: 'center',
    height: theme.spacing(4)
  })
);

const ReplyListing = styled('div', { name, slot: 'ReplyListing' })(
  ({ theme }) => ({
    paddingLeft: theme.spacing(5),
    '& > div': {
      position: 'relative',
      '&:before': {
        content: '""',
        width: 12,
        borderTop: 'solid 1px',
        borderTopColor: theme.palette.border?.secondary,
        position: 'absolute',
        left: theme.spacing(-3),
        top: theme.spacing(3)
      }
    }
  })
);

const ViewMoreReplyButton = styled('div', { name, slot: 'AvatarWrapper' })(
  ({ theme }) => ({
    paddingLeft: theme.spacing(10),
    paddingTop: theme.spacing(1),
    paddingBottom: theme.spacing(1),
    color: theme.palette.primary.main,
    display: 'flex',
    alignItems: 'center',
    '&:hover': {
      textDecoration: 'underline'
    }
  })
);

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

const compareDate = (a, b, sortKeySetting) => {
  return (
    new Date(a[sortKeySetting]).valueOf() -
    new Date(b[sortKeySetting]).valueOf()
  );
};

export default function Comment({
  item,
  user,
  identity,
  handleAction,
  state,
  extra_data,
  actions,
  parent_user,
  identityResource
}: CommentItemProps) {
  const children = item?.children;
  const [loadingMore, setLoadingMore] = React.useState(false);
  const { i18n, jsxBackend, useTheme, dispatch } = useGlobal();
  const theme = useTheme();
  const {
    ItemActionMenu,
    ReactionResult,
    ReplyItemView,
    ReactionActButton,
    ReplyActButton,
    RemovePreviewActButton,
    getSetting,
    useGetItems,
    useGetItem,
    usePageParams,
    HistoryEditedCommentButton,
    useLoggedIn
  } = useGlobal();
  const { comment_id } = usePageParams();
  const sortKeySetting = getSetting('comment.sort_by_key') || 'creation_date';
  const isLoggedIn = useLoggedIn();

  const CommentComposer = jsxBackend.get('CommentComposer');
  const dataChildren = useGetItems(children);
  const [highlight, setHighlight] = React.useState(false);
  const [isPreviewHidden, setIsPreviewHidden] = React.useState(false);
  const pagingReplyId = `comment/${identity.replace(/\./g, '_')}`;
  const pagingReplyData = useGetItem(`pagination.${pagingReplyId}`);
  const endedMore = pagingReplyData?.ended;

  React.useEffect(() => {
    if (children && children.length) {
      setLoadingMore(false);
    }
  }, [children]);

  React.useEffect(() => {
    if (comment_id) {
      setHighlight(parseInt(comment_id) === item?.id);
      setTimeout(() => {
        setHighlight(false);
      }, 2000);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [comment_id]);

  const handlePreviewHiddenComment = () => {
    if (!isPreviewHidden) {
      dispatch({
        type: 'comment/preview',
        payload: { identity: item._identity }
      });
      setIsPreviewHidden(true);
    } else setIsPreviewHidden(false);
  };

  if (!item || !user) return null;

  const remainChild = Math.min(item.child_total - (children?.length || 0), 10);
  const isThreadDisplay = getSetting('comment.enable_thread');

  const {
    text,
    editText,
    creation_date,
    most_reactions,
    statistic,
    link,
    is_edited,
    is_hidden
  } = item;

  const preFetchingComment = Object.values(
    item?.preFetchingComment || {}
  ).filter(item => item?.isLoading === true);

  const showRemovePreviewButton =
    extra_data?.extra_type === 'link' && item?.extra?.can_remove_link_preview;

  const handleClickMore = () => {
    setLoadingMore(true);
    actions.viewMoreReplies({ pagingId: pagingReplyId });
  };

  const childrenSort = dataChildren.sort((a, b) =>
    compareDate(a, b, sortKeySetting)
  );

  return (
    <div
      data-testid="comment"
      id={`comment-${item.id}`}
      data-author={user.full_name}
    >
      <Box pt={1}>
        {item.isEditing ? (
          <EditContent
            text={editText}
            extra_data={extra_data}
            handleAction={handleAction}
            identity={identity}
            actions={actions}
          />
        ) : (
          <ItemOuter isLoading={item.isLoading}>
            <AvatarWrapper>
              <UserAvatar user={user as any} size={32} />
            </AvatarWrapper>
            <ItemInner>
              <BoxWrapper p={0} borderRadius={2} highlight={highlight}>
                <Box sx={{ position: 'relative', zIndex: 2 }}>
                  <ItemName>
                    <UserNameStyled
                      hoverCard
                      to={`/user/${user.id}`}
                      children={user.full_name}
                    />
                    <Link color="inherit" to={link}>
                      <FromNowStyled value={creation_date} shorten />
                    </Link>
                    {HistoryEditedCommentButton && is_edited ? (
                      <HistoryEditedCommentButton
                        data-testid="historyCommentButton"
                        minimize
                        sx={{ marginLeft: '8px', textTransform: 'capitalize' }}
                        identity={identity}
                        handleAction={handleAction}
                      />
                    ) : null}
                  </ItemName>
                  <Content
                    isHidden={is_hidden}
                    isPreviewHidden={isPreviewHidden}
                    text={text}
                    extra_data={!item?.hideExtraData ? extra_data : null}
                  />
                </Box>
              </BoxWrapper>
              {isLoggedIn && (
                <ReactionsWrapper className={'dotSeparators'}>
                  {is_hidden ? (
                    <Tooltip
                      componentsProps={{
                        tooltip: { sx: { maxWidth: '210px !important' } }
                      }}
                      title={i18n.formatMessage({
                        id: 'preview_hidden_comment_tooltip'
                      })}
                    >
                      <span>
                        <ActButton
                          data-testid="previewHiddenButton"
                          onClick={handlePreviewHiddenComment}
                          label={i18n.formatMessage({ id: 'preview' })}
                          color={
                            isPreviewHidden ? theme.palette.primary.main : ''
                          }
                          minimize
                        />
                      </span>
                    </Tooltip>
                  ) : (
                    <>
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
                          openReplyComposer={actions.openReplyComposer}
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
                    </>
                  )}
                  <ItemActionMenu
                    identity={identity}
                    state={state}
                    handleAction={handleAction}
                    size="smaller"
                    sx={{ visibility: 'hidden' }}
                    className={'itemActionMenu'}
                  />
                </ReactionsWrapper>
              )}
            </ItemInner>
          </ItemOuter>
        )}
      </Box>
      {0 < remainChild && !endedMore ? (
        <ViewMoreReplyButton
          aria-label="reply"
          role="button"
          onClick={handleClickMore}
        >
          {children?.length ? (
            <span>
              {i18n.formatMessage(
                { id: 'view_previous_reply' },
                { value: remainChild }
              )}
            </span>
          ) : (
            <>
              <LineIcon icon="ico-forward" />{' '}
              <span>
                {i18n.formatMessage(
                  { id: 'number_reply' },
                  { value: item?.child_total }
                )}
              </span>
            </>
          )}
          {loadingMore && (
            <CircularProgress sx={{ marginLeft: '4px' }} size={12} />
          )}
        </ViewMoreReplyButton>
      ) : null}
      {!is_hidden && (childrenSort?.length || preFetchingComment?.length) ? (
        <ReplyListing>
          {childrenSort?.length
            ? childrenSort.map(item => (
                <ReplyItemView
                  identity={`comment.entities.comment.${item.id}`}
                  openReplyComposer={actions.openReplyComposer}
                  key={`${item?.id}`}
                  parent_user={parent_user}
                />
              ))
            : null}
          {preFetchingComment?.length
            ? preFetchingComment?.map(item => (
                <PreFetchComment key={item.key} text={item.text} />
              ))
            : null}
        </ReplyListing>
      ) : null}
      {item.parent_id && state.commentOpened ? null : (
        <ReplyListing>
          {CommentComposer && (
            <CommentComposer
              open={state.commentOpened}
              focus={state.commentFocused}
              replyUser={state.replyUser}
              margin="dense"
              identity={identity}
              identityResource={identityResource}
              isReply
              parentUser={parent_user}
            />
          )}
        </ReplyListing>
      )}
    </div>
  );
}
