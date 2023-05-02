import { useSortComment } from '@metafox/comment/hooks';
import { getTaggedFriendsPhotoSelector } from '@metafox/core/selectors/status';
import { FeedItemViewProps } from '@metafox/feed';
import ProfileLink from '@metafox/feed/components/FeedItemView/ProfileLink';
import FeedStatusView from '@metafox/feed/components/FeedStatus/FeedStatusView';
import {
  getItemSelector,
  GlobalState,
  Link,
  useGlobal,
  useLoggedIn
} from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { ScrollContainer } from '@metafox/layout';
import {
  FeedStatistic,
  FromNow,
  LineIcon,
  PrivacyIcon,
  UserAvatar,
  Statistic
} from '@metafox/ui';
import { Box, Divider, Skeleton } from '@mui/material';
import { styled } from '@mui/material/styles';
import clsx from 'clsx';
import * as React from 'react';
import { useSelector } from 'react-redux';
import useStyles from './styles';
import TaggedFriendsPhoto from './TaggedFriendsPhoto';
import TaggedFriends from './TaggedFriends';
import TaggedPlace from './TaggedPlace';

const name = 'ItemDetailInteractionInModal';

const ModalWrapper = styled(Box, {
  name,
  slot: 'root'
})(({ theme }) => ({
  borderTopRightRadius: theme.shape.borderRadius,
  borderBottomRightRadius: theme.shape.borderRadius,
  backgroundColor: theme.palette.background.paper,
  paddingBottom: 0,
  display: 'flex',
  flexFlow: 'column',
  height: '100%',
  justifyContent: 'space-between',
  minWidth: '420px',
  [theme.breakpoints.down('sm')]: {
    width: '100%',
    maxHeight: 'none',
    borderRadius: 0,
    minWidth: 'auto'
  }
}));

const ContentWrapper = styled('div', {
  name,
  slot: 'contentWrapper'
})(({ theme }) => ({
  flex: 1,
  minHeight: 0,
  display: 'flex',
  flexFlow: 'column',
  position: 'relative'
}));

const Header = styled('div', {
  name,
  slot: 'header'
})(({ theme }) => ({
  padding: theme.spacing(2),
  display: 'flex',
  flexDirection: 'row'
}));

const BodyWrapper = styled('div', {
  name,
  slot: 'body'
})(({ theme }) => ({
  flex: 1,
  minHeight: 0
}));

const Footer = styled('div', {
  name,
  slot: 'footer',
  shouldForwardProp: prop => prop !== 'maxHeight'
})<{ maxHeight?: number }>(({ theme, maxHeight }) => ({
  borderBottomRightRadius: theme.shape.borderRadius,
  backgroundColor: theme.palette.background.paper,
  padding: theme.spacing(0, 2),
  zIndex: 99,
  borderTop: `1px solid ${theme.palette.border.secondary}`,
  minHeight: theme.spacing(6),
  ...(maxHeight && {
    maxHeight
  }),
  [theme.breakpoints.down('sm')]: {
    position: 'unset',
    maxHeight: 'none'
  }
}));

const InfoStyled = styled('div', {
  name,
  slot: 'info'
})(({ theme }) => ({
  marginBottom: theme.spacing(2),
  fontSize: theme.mixins.pxToRem(15),
  color: theme.palette.text.primary
}));
const ContentStyled = styled('div', {
  name,
  slot: 'ContentStyled'
})(({ theme }) => ({
  marginBottom: theme.spacing(1)
}));

const PrivacyBlockStyled = styled('div', {
  name,
  slot: 'privacyBlock'
})(({ theme }) => ({
  display: 'flex',
  flexDirection: 'row',
  alignItems: 'center',
  color: theme.palette.text.secondary
}));

const ProfileLinkStyled = styled(Link, {
  name,
  slot: 'profileLink'
})(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(15),
  fontWeight: theme.typography.fontWeightBold,
  paddingRight: theme.spacing(0.5),
  color: theme.palette.text.primary
}));

const HeadlineSpan = styled('span', { name: 'HeadlineSpan' })(({ theme }) => ({
  paddingRight: theme.spacing(0.5),
  color: theme.palette.text.secondary
}));

const ItemDetailInteractionInModal = ({
  identity,
  item,
  user,
  loading,
  commentlistingComponent,
  startFooterItems,
  commentComposerProps,
  statisticDisplay
}: FeedItemViewProps & {
  loading?: boolean;
  commentlistingComponent?: React.FC;
  startFooterItems: any;
  commentComposerProps?: Record<string, any>;
  statisticDisplay?: string;
}) => {
  const commentInputRef = React.useRef();
  const wrapperModalRef = React.useRef(null);
  const headerRef = React.useRef(null);
  const footerRef = React.useRef(null);
  const {
    CommentList,
    ReactionActButton,
    CommentReaction,
    CommentActButton,
    ShareActButton,
    ItemActionMenu,
    jsxBackend,
    useActionControl,
    i18n
  } = useGlobal();
  const [sortType, setSortType] = useSortComment();
  const [handleAction, state] = useActionControl(identity, {
    commentFocused: false,
    menuOpened: false,
    commentOpened: true,
    commentInputRef
  });
  const tagged_friends = useSelector((state: GlobalState) =>
    getTaggedFriendsPhotoSelector(state, item)
  );

  const owner = useSelector((state: GlobalState) =>
    getItemSelector(state, item?.owner)
  );

  const classes = useStyles();
  const loggedIn = useLoggedIn();
  const viewMoreComments = (payload, meta) =>
    handleAction('comment/viewMoreComments', payload, meta);

  if (loading && !item?.extra) {
    // cheat check extra is mean have fetch data detail before
    return <LoadingSkeleton />;
  }

  const PendingGroupPreview = jsxBackend.get(
    'photo.itemView.pendingReviewCard'
  );

  if (!item || !user) return null;

  const { info, statistic, most_reactions, extra, location } = item;
  const CommentComposer = jsxBackend.get('CommentComposer');

  return (
    <ModalWrapper>
      <ContentWrapper ref={wrapperModalRef}>
        <PendingGroupPreview item={item} />

        <Header ref={headerRef}>
          <Box pr={1.5}>
            <UserAvatar user={user} size={48} data-testid="author" />
          </Box>
          <Box py={0.5} flex={1}>
            <div className={classes.headerHeadline}>
              <ProfileLinkStyled
                to={`/${user.user_name}`}
                children={user.full_name}
                hoverCard={`/user/${user.id}`}
                data-testid="headline"
              />
              {owner?.resource_name !== user?.resource_name && (
                <HeadlineSpan>
                  {i18n.formatMessage(
                    {
                      id: 'to_parent_user'
                    },
                    {
                      icon: () => (
                        <LineIcon
                          icon="ico-caret-right"
                          className={classes.caretIcon}
                        />
                      ),
                      parent_user: () => (
                        <ProfileLink
                          user={owner}
                          className={classes.profileLink}
                        />
                      )
                    }
                  )}
                </HeadlineSpan>
              )}
            </div>
            <PrivacyBlockStyled className={clsx('dotSeparators')}>
              <FromNow value={item.creation_date} data-testid="creationDate" />
              {statisticDisplay ? (
                <Statistic
                  values={item.statistic}
                  display={statisticDisplay}
                  component={'span'}
                  skipZero={false}
                />
              ) : null}
              <PrivacyIcon
                value={item.privacy}
                item={item?.privacy_detail}
                data-testid="iconPrivacy"
              />
            </PrivacyBlockStyled>
          </Box>
          <ItemActionMenu
            identity={identity}
            state={state}
            handleAction={handleAction}
          />
        </Header>
        <BodyWrapper>
          <Box
            sx={{
              maxHeight: '100%',
              display: 'flex',
              flexDirection: 'column',
              height: '100%'
            }}
          >
            <ScrollContainer autoHide autoHeight autoHeightMax={'100%'}>
              <Box px={2}>
                <Box mb={2}>
                  <ContentStyled>
                    <FeedStatusView status={item?.text ?? item?.description} />
                  </ContentStyled>
                  {info && (
                    <InfoStyled>
                      <HtmlViewer html={info} />
                    </InfoStyled>
                  )}
                  {tagged_friends?.length ? (
                    <HeadlineSpan className={classes.statusRoot}>
                      {item.resource_name === 'photo' ? (
                        <TaggedFriendsPhoto
                          item_type={'photo'}
                          item_id={item.id}
                          total={tagged_friends.length}
                          users={tagged_friends}
                          className={classes.profileLink}
                        />
                      ) : (
                        <TaggedFriends
                          item_type={item.item_type}
                          item_id={item.id}
                          total={tagged_friends.length}
                          users={tagged_friends}
                          className={classes.profileLink}
                        />
                      )}
                      {location ? (
                        <Box component="span" ml={0.5}>
                          {i18n.formatMessage(
                            {
                              id: 'at_tagged_place'
                            },
                            {
                              name: location.address,
                              bold: () => <TaggedPlace place={location} />
                            }
                          )}
                        </Box>
                      ) : null}
                    </HeadlineSpan>
                  ) : null}
                </Box>
                <FeedStatistic
                  id-tid="statistic"
                  handleAction={handleAction}
                  identity={identity}
                  reactions={most_reactions}
                  statistic={statistic}
                />
                <CommentReaction>
                  {extra?.can_like && (
                    <ReactionActButton
                      id-tid="reaction"
                      reacted={item.user_reacted}
                      identity={identity}
                      handleAction={handleAction}
                    />
                  )}
                  {extra?.can_comment && (
                    <CommentActButton
                      id-tid="comment"
                      identity={identity}
                      handleAction={handleAction}
                    />
                  )}
                  {extra?.can_share && (
                    <ShareActButton
                      handleAction={handleAction}
                      id-tid="share"
                      identity={identity}
                    />
                  )}
                </CommentReaction>
                <Divider />
                {commentlistingComponent ? (
                  commentlistingComponent
                ) : CommentList ? (
                  <CommentList
                    id-tid="comment_list"
                    handleAction={handleAction}
                    data={item.related_comments}
                    total_hidden={
                      item?.related_comments_statistic?.total_hidden
                    }
                    viewMoreComments={viewMoreComments}
                    total_comment={statistic?.total_comment}
                    total_reply={statistic?.total_reply}
                    identity={identity}
                    setSortType={setSortType}
                    sortType={sortType}
                    open={state?.commentOpened}
                    isDetailPage
                  />
                ) : null}
              </Box>
            </ScrollContainer>
          </Box>
        </BodyWrapper>
        <Footer ref={footerRef}>
          {startFooterItems ? jsxBackend.render(startFooterItems) : null}
          {loggedIn && extra?.can_comment && CommentComposer ? (
            <CommentComposer
              id-tid="comment_composer"
              identity={identity}
              open={state?.commentOpened}
              focus={state.commentFocused}
              {...commentComposerProps}
            />
          ) : null}
        </Footer>
      </ContentWrapper>
    </ModalWrapper>
  );
};

const LoadingSkeleton = () => {
  return (
    <ModalWrapper>
      <Box>
        <Header>
          <Box pr={1.5}>
            <Skeleton variant="circular" width={40} height={40} />
          </Box>
          <Box py={0.5} flex={1}>
            <div>
              <Skeleton variant="text" component="div" />
            </div>
            <PrivacyBlockStyled>
              <Skeleton variant="text" width={120} />
            </PrivacyBlockStyled>
          </Box>
        </Header>
        <Box px={2} mt={4}>
          <Box sx={{ display: 'flex', margin: '0 -8px' }}>
            <Box p={1} flex={1}>
              <Skeleton variant="rounded" height={32} />
            </Box>
            <Box p={1} flex={1}>
              <Skeleton variant="rounded" height={32} />
            </Box>
            <Box p={1} flex={1}>
              <Skeleton variant="rounded" height={32} />
            </Box>
          </Box>
        </Box>
        <Box px={2} mt={4}>
          <Skeleton variant="text" />
          <Skeleton variant="text" />
          <Skeleton variant="text" />
        </Box>
      </Box>
    </ModalWrapper>
  );
};

ItemDetailInteractionInModal.LoadingSkeleton = LoadingSkeleton;

export default ItemDetailInteractionInModal;
