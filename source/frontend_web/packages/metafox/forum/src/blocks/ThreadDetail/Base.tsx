import { ThreadDetailViewProps as ItemProps } from '@metafox/forum/types';
import {
  Link,
  useGlobal,
  useResourceAction,
  GlobalState,
  getItemSelector
} from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import {
  AttachmentItem,
  DotSeparator,
  FeaturedFlag,
  FormatDate,
  ItemAction,
  ItemTitle,
  ItemUserShape,
  SponsorFlag,
  UserAvatar,
  RichTextViewMore,
  LineIcon
} from '@metafox/ui';
import {
  Box,
  styled,
  Typography,
  useMediaQuery,
  useTheme
} from '@mui/material';
import React from 'react';
import LoadingSkeleton from './LoadingSkeleton';
import { APP_FORUM, RESOURCE_FORUM_THREAD } from '@metafox/forum/constants';
import { useSelector } from 'react-redux';
import ProfileLink from '@metafox/feed/components/FeedItemView/ProfileLink';

const name = 'ThreadDetailView';

export type Props = ItemProps;

const ContentWrapper = styled('div', { name, slot: 'ContentWrapper' })(
  ({ theme }) => ({
    backgroundColor: theme.mixins.backgroundColor('paper'),
    borderRadius: theme.spacing(1)
  })
);

const ThreadViewContainer = styled('div', {
  name,
  slot: 'threadViewContainer'
})(({ theme }) => ({
  width: '100%',
  marginLeft: 'auto',
  marginRight: 'auto',
  padding: `${theme.spacing(2)} ${theme.spacing(2)} 0 ${theme.spacing(2)}`,
  position: 'relative',
  borderBottomLeftRadius: theme.shape.borderRadius,
  borderBottomRightRadius: theme.shape.borderRadius
}));
const AvatarWrapper = styled('div', { name, slot: 'AvatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1.5)
  })
);
const ThreadContent = styled('div', { name, slot: 'threadContent' })(
  ({ theme }) => ({
    fontSize: theme.mixins.pxToRem(15),
    lineHeight: 1.33,
    marginTop: theme.spacing(3),
    '& p + p': {
      marginBottom: theme.spacing(2.5)
    }
  })
);
const TagItem = styled('div', { name, slot: 'tagItem' })(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(13),
  fontWeight: theme.typography.fontWeightBold,
  borderRadius: 4,
  background:
    theme.palette.mode === 'light'
      ? theme.palette.background.default
      : theme.palette.action.hover,
  marginRight: theme.spacing(1),
  marginBottom: theme.spacing(1),
  padding: theme.spacing(0, 1.5),
  height: theme.spacing(3),
  lineHeight: theme.spacing(3),
  display: 'block',
  color: theme.palette.mode === 'light' ? '#121212' : '#fff'
}));
const AttachmentTitle = styled('div', { name, slot: 'attachmentTitle' })(
  ({ theme }) => ({
    fontSize: theme.mixins.pxToRem(18),
    marginTop: theme.spacing(4),
    color: theme.palette.text.secondary,
    fontWeight: theme.typography.fontWeightBold
  })
);
const Attachment = styled('div', { name, slot: 'attachment' })(({ theme }) => ({
  width: '100%',
  display: 'flex',
  flexWrap: 'wrap',
  marginTop: theme.spacing(2),
  justifyContent: 'space-between'
}));
const AttachmentItemWrapper = styled('div', {
  name,
  slot: 'attachmentItemWrapper'
})(({ theme }) => ({
  marginTop: theme.spacing(2),
  flexGrow: 0,
  flexShrink: 0,
  flexBasis: 'calc(50% - 8px)',
  minWidth: 300
}));

const OwnerStyled = styled(ProfileLink, { name: 'OwnerStyled' })(
  ({ theme }) => ({
    fontWeight: theme.typography.fontWeightBold,
    color: theme.palette.text.primary,
    fontSize: theme.mixins.pxToRem(15),
    '&:hover': {
      textDecoration: 'underline'
    }
  })
);

const HeadlineSpan = styled('span', { name: 'HeadlineSpan' })(({ theme }) => ({
  paddingRight: theme.spacing(0.5),
  color: theme.palette.text.secondary
}));

export default function DetailView({
  user,
  identity,
  item,
  state,
  actions,
  handleAction
}: ItemProps) {
  const {
    ItemActionMenu,
    ItemDetailInteraction,
    useGetItems,
    useGetItem,
    i18n,
    jsxBackend
  } = useGlobal();
  const [isShowPost, setToggleShowPost] = React.useState(true);
  const attachments = useGetItems(item?.attachments);
  const itemAttachItem = useGetItem(item?.item);
  const theme = useTheme();
  const isSmallScreen = useMediaQuery(theme.breakpoints.down('sm'));
  const dataSourceCommentStatistic = useResourceAction(
    APP_FORUM,
    RESOURCE_FORUM_THREAD,
    'viewPosters'
  );

  const owner = useSelector((state: GlobalState) =>
    getItemSelector(state, item?.owner)
  );
  const PendingCard = jsxBackend.get('core.itemView.pendingReviewCard');
  const PollView = jsxBackend.get('poll.embedItem.insideFeedItem');
  const PostListing = jsxBackend.get('forum_post.block.detailListingBlock');
  const PostForm = jsxBackend.get('forum_post.block.addForm');

  if (!user || !item) return null;

  const { tags, description, item: itemAttach } = item;

  const pollIdentity =
    itemAttach && itemAttach.startsWith('poll.') ? itemAttach : null;

  const handleActionCommentStatistic = () => {
    setToggleShowPost(prev => !prev);
  };

  return (
    <>
      <Block testid={`detailview ${item.resource_name}`}>
        <BlockContent>
          <ContentWrapper>
            {PendingCard && item?.is_pending ? (
              <Box sx={{ px: 2, pt: 2 }}>
                <PendingCard sx item={{ ...item }} />
              </Box>
            ) : null}
            <ThreadViewContainer>
              <ItemAction sx={{ position: 'absolute', top: 8, right: 8 }}>
                <ItemActionMenu
                  identity={identity}
                  icon={'ico-dottedmore-vertical-o'}
                  state={state}
                  menuName="detailActionMenu"
                  handleAction={handleAction}
                  size="smaller"
                />
              </ItemAction>
              <ItemTitle variant="h3" component={'div'} my={0} showFull>
                <FeaturedFlag variant="itemView" value={item.is_featured} />
                <SponsorFlag variant="itemView" value={item.is_sponsor} />
                <Typography
                  component="h1"
                  variant="h3"
                  sx={{
                    pr: 2.5,
                    display: { sm: 'inline', xs: 'block' },
                    verticalAlign: 'middle'
                  }}
                >
                  {item?.title}
                </Typography>
              </ItemTitle>
              <Box mt={2} display="flex">
                <AvatarWrapper>
                  <UserAvatar user={user as ItemUserShape} size={48} />
                </AvatarWrapper>
                <Box>
                  <Link
                    variant="body1"
                    color={'text.primary'}
                    to={user.link}
                    children={user?.full_name}
                    hoverCard={`/user/${user.id}`}
                    sx={{ fontWeight: 'bold', mr: 0.5 }}
                  />
                  {owner?.resource_name !== user?.resource_name && (
                    <HeadlineSpan>
                      {i18n.formatMessage(
                        {
                          id: 'to_parent_user'
                        },
                        {
                          icon: () => <LineIcon icon="ico-caret-right" />,
                          parent_user: () => <OwnerStyled user={owner} />
                        }
                      )}
                    </HeadlineSpan>
                  )}
                  <DotSeparator sx={{ color: 'text.secondary', mt: 1 }}>
                    <FormatDate
                      data-testid="publishedDate"
                      value={item?.creation_date}
                      format="MMMM DD, yyyy"
                    />
                  </DotSeparator>
                </Box>
              </Box>
              <ThreadContent>
                <RichTextViewMore maxHeight="300px">
                  <HtmlViewer html={description || ''} />
                </RichTextViewMore>
                {item?.modification_date ? (
                  <DotSeparator
                    sx={{ color: 'text.secondary', mt: 1, fontStyle: 'italic' }}
                  >
                    <FormatDate
                      data-testid="modifyDate"
                      value={item?.modification_date}
                      format="MMMM DD, yyyy"
                      phrase="last_update_on_time"
                    />
                  </DotSeparator>
                ) : null}
              </ThreadContent>
              {PollView && pollIdentity && !itemAttachItem?.error ? (
                <Box mt={4}>
                  <PollView identity={pollIdentity} />
                </Box>
              ) : null}
              {tags?.length > 0 ? (
                <Box mt={4} display="flex" flexWrap="wrap">
                  {tags.map(tag => (
                    <TagItem key={tag}>
                      <Link to={`/forum/search?q=%23${tag}`}>{tag}</Link>
                    </TagItem>
                  ))}
                </Box>
              ) : null}
              {attachments?.length > 0 && (
                <>
                  <AttachmentTitle>
                    {i18n.formatMessage({ id: 'attachments' })}
                  </AttachmentTitle>
                  <Attachment>
                    {attachments.map((item: any) => (
                      <AttachmentItemWrapper key={item.id.toString()}>
                        <AttachmentItem
                          fileName={item.file_name}
                          downloadUrl={item.download_url}
                          isImage={item.is_image}
                          fileSizeText={item.file_size_text}
                          size={isSmallScreen ? 'mini' : 'large'}
                          image={item?.image}
                        />
                      </AttachmentItemWrapper>
                    ))}
                  </Attachment>
                </>
              )}
              <ItemDetailInteraction
                identity={identity}
                state={state}
                handleAction={handleAction}
                messageCommentStatistic={'total_reply'}
                dataSourceCommentStatistic={dataSourceCommentStatistic}
                forceHideCommentList
                handleActionCommentStatistic={handleActionCommentStatistic}
              />
            </ThreadViewContainer>
          </ContentWrapper>
        </BlockContent>
      </Block>
      <Box
        sx={{ borderRadius: 1, overflow: 'hidden' }}
        className={!isShowPost && 'srOnly'}
      >
        <PostListing />
        <PostForm blockLayout="Forum Post Form Detail Thread" />
      </Box>
    </>
  );
}

DetailView.LoadingSkeleton = LoadingSkeleton;
DetailView.displayName = 'ThreadItem_DetailView';
