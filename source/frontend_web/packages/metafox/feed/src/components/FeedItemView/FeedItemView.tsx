import { useGlobal } from '@metafox/framework';
import { FeedStatistic, ItemView, LineIcon } from '@metafox/ui';
import { Divider, styled, Box, Typography } from '@mui/material';
import * as React from 'react';
import { FeedItemViewProps as Props } from '../../types';
import FeedItemContent from './FeedItemContent';
import FeedItemHiddenView from './FeedItemHiddenView';
import { LoadingSkeleton } from './LoadingSkeleton';
import useStyles from './styles';
import { SORT_OLDEST, SORT_NEWEST } from '@metafox/comment';
import { useSortComment } from '@metafox/comment/hooks';
import { isArray } from 'lodash';

export const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1.5)
  })
);

const ItemViewStyled = styled(ItemView, { name: 'ItemView' })(({ theme }) => ({
  overflow: 'inherit'
}));

const Notice = styled('div', { name: 'TableStyled' })(({ theme }) => ({
  textAlign: 'center',
  padding: theme.spacing(2),
  color: theme.palette.text.hint,
  fontWeight: theme.typography.fontWeightSemiBold
}));

const FeedItemView = ({
  identity,
  item,
  user,
  actions,
  handleAction,
  state,
  wrapAs,
  wrapProps,
  itemProps,
  parent_user
}: Props) => {
  const {
    i18n,
    CommentList,
    SortCommentList,
    jsxBackend,
    CommentActButton,
    ShareActButton,
    ReactionActButton,
    CommentReaction,
    useSession,
    dialogBackend,
    useIsMobile,
    usePageParams
  } = useGlobal();
  const classes = useStyles();
  const session = useSession();
  const pageParams = usePageParams();
  const myRef = React.useRef(null);
  const [sortType, setSortType, loadingSort, setLoadingSort] = useSortComment();
  const [showSort, setShowSort] = React.useState(false);
  const [visible, setVisible] = React.useState<boolean>(true);

  const isMobile = useIsMobile();

  React.useEffect(() => {
    if (item?.is_just_hide)
      myRef.current?.scrollIntoView({
        behavior: 'smooth',
        block: 'center',
        inline: 'nearest'
      });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [item?.is_just_hide]);

  if (!item || !user) return null;

  const CommentComposer = jsxBackend.get('CommentComposer');

  const { statistic, most_reactions, like_phrase } = item;

  const { menuName = 'itemActionMenu' } = itemProps;

  const handleLayoutWithAction = (type?: string, payload?: unknown) => {
    const acceptTypes = ['toggleItemComments', 'onPressedCommentActButton'];

    if (isMobile && acceptTypes.includes(type)) {
      dialogBackend.present({
        component: 'comment.dialog.commentList',
        props: {
          identity,
          isFocus: type === 'onPressedCommentActButton',
          handleAction,
          viewMoreComments: actions.viewMoreComments
        }
      });

      return;
    }

    handleAction(type, payload);
  };

  const handleClickComposer: React.MouseEventHandler<HTMLDivElement> = e => {
    if (!isMobile) return;

    e.stopPropagation();
    handleLayoutWithAction('onPressedCommentActButton');
  };

  if (!visible) return null;

  let status = '';

  if (!item.extra?.can_comment && item.item_type !== 'user') {
    status = i18n.formatMessage({
      id: 'comment_turn_off'
    });

    if (!item.extra?.can_like && !item.extra?.can_share) {
      status = i18n.formatMessage({ id: 'read_only_post' });
    }

    if (
      statistic?.total_comment ||
      statistic?.total_like ||
      statistic?.total_share
    ) {
      status = i18n.formatMessage({
        id: 'comment_turn_off'
      });
    }

    if (item.item_type === 'friend') {
      status = '';
    }
  }

  const isPinned =
    isArray(item.pins) &&
    // eslint-disable-next-line eqeqeq
    item.pins.findIndex(pinId => pinId == pageParams.profile_id) >= 0;

  return (
    <ItemViewStyled
      wrapProps={wrapProps}
      wrapAs={wrapAs}
      testid={`${item.resource_name}`}
      data-eid={identity}
      ref={myRef}
      id={`homefeed_${identity}`}
    >
      {item.is_just_hide ? (
        <FeedItemHiddenView
          item={item}
          classes={classes}
          handleAction={handleAction}
        />
      ) : (
        <>
          {isPinned && (
            <Typography
              variant="body2"
              color="text.secondary"
              paddingBottom={2}
              display="flex"
              alignItems={'center'}
            >
              <LineIcon icon="ico-thumb-tack-o" sx={{ paddingRight: 1 }} />
              {i18n.formatMessage({ id: 'pinned_post' })}
            </Typography>
          )}
          <FeedItemContent
            menuName={menuName}
            identity={identity}
            state={state}
            handleAction={handleAction}
            setVisible={setVisible}
          />
          {session.loggedIn && (
            <div className={classes.actionButtonStaticsWrapper}>
              <div className={classes.reactionWrapper}>
                {CommentReaction ? (
                  <CommentReaction>
                    {session.loggedIn &&
                    item.extra?.can_like &&
                    ReactionActButton ? (
                      <ReactionActButton
                        onlyIcon={!isMobile}
                        reacted={item.user_reacted}
                        identity={identity}
                        handleAction={handleAction}
                      />
                    ) : null}
                    {session.loggedIn &&
                    item.extra?.can_comment &&
                    CommentActButton ? (
                      <CommentActButton
                        onlyIcon={!isMobile}
                        identity={identity}
                        handleAction={handleLayoutWithAction}
                      />
                    ) : null}
                    {session.loggedIn &&
                    item.extra.can_share &&
                    ShareActButton ? (
                      <ShareActButton
                        handleAction={handleAction}
                        identity={identity}
                        onlyIcon={!isMobile}
                      />
                    ) : null}
                  </CommentReaction>
                ) : null}
              </div>
              <div className={classes.feedStatisticWrapper}>
                <FeedStatistic
                  handleAction={handleLayoutWithAction}
                  identity={identity}
                  reactions={most_reactions}
                  message={like_phrase}
                  statistic={statistic}
                />
              </div>
            </div>
          )}
          {(statistic?.total_comment || item.extra?.can_comment) &&
          state.commentOpened &&
          session.loggedIn ? (
            <Divider />
          ) : null}
          {[SORT_OLDEST, SORT_NEWEST].includes(sortType) ? (
            <>
              {!isMobile && showSort ? (
                <Box mt={1}>
                  <SortCommentList value={sortType} setValue={setSortType} />
                </Box>
              ) : null}
              {session.loggedIn &&
              item.extra.can_comment &&
              CommentComposer &&
              !loadingSort ? (
                <Box onClickCapture={handleClickComposer}>
                  {React.createElement(CommentComposer, {
                    identity,
                    parentUser: parent_user,
                    open: state.commentOpened,
                    focus: state.commentFocused
                  })}
                </Box>
              ) : null}
              {!isMobile && CommentList && (
                <CommentList
                  identity={identity}
                  handleAction={handleAction}
                  open={state.commentOpened}
                  data={item.related_comments}
                  total_hidden={item?.related_comments_statistic?.total_hidden}
                  viewMoreComments={actions.viewMoreComments}
                  total_comment={statistic?.total_comment}
                  total_reply={statistic?.total_reply}
                  parent_user={parent_user}
                  sortType={sortType}
                  forceHideSort
                  setShowSort={setShowSort}
                  setLoadingSort={setLoadingSort}
                />
              )}
            </>
          ) : (
            <>
              {!isMobile && CommentList && (
                <CommentList
                  identity={identity}
                  handleAction={handleAction}
                  open={state.commentOpened}
                  data={item.related_comments}
                  total_hidden={item?.related_comments_statistic?.total_hidden}
                  viewMoreComments={actions.viewMoreComments}
                  total_comment={statistic?.total_comment}
                  total_reply={statistic?.total_reply}
                  parent_user={parent_user}
                  sortType={sortType}
                  setSortType={setSortType}
                  setLoadingSort={setLoadingSort}
                />
              )}
              {!loadingSort ? (
                <>
                  {item.related_comments &&
                  item.related_comments.length > 0 &&
                  state.commentOpened &&
                  !isMobile ? (
                    <Divider sx={{ mt: 0.5 }} />
                  ) : null}
                  {session.loggedIn &&
                  item.extra.can_comment &&
                  CommentComposer ? (
                    <div onClickCapture={handleClickComposer}>
                      {React.createElement(CommentComposer, {
                        identity,
                        parentUser: parent_user,
                        open: state.commentOpened,
                        focus: state.commentFocused
                      })}
                    </div>
                  ) : null}
                </>
              ) : null}
            </>
          )}
          {status !== '' ? (
            <div>
              {(statistic?.total_like ||
                statistic?.total_share ||
                item.extra?.can_like ||
                item.extra?.can_share ||
                item.extra?.can_comment) &&
              !statistic?.total_comment ? (
                <Divider />
              ) : null}
              <Notice>{status}</Notice>
            </div>
          ) : null}
          {!session.loggedIn && item.extra?.can_comment && (
            <Box sx={{ paddingBottom: '16px' }}></Box>
          )}
        </>
      )}
    </ItemViewStyled>
  );
};

FeedItemView.LoadingSkeleton = LoadingSkeleton;

export default FeedItemView;
