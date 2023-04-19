/**
 * @type: block
 * name: blog.block.blogView
 * title: Blog Detail
 * keywords: blog
 * description: Display blog detail
 * experiment: true
 */

import { BlogDetailViewProps as Props } from '@metafox/blog';
import actionCreators from '@metafox/blog/actions/blogItemActions';
import { BlogDetailViewProps as ItemProps } from '@metafox/blog/types';
import {
  connectItemView,
  connectSubject,
  createBlock,
  Link,
  useGlobal,
  GlobalState,
  getItemSelector
} from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import {
  AttachmentItem,
  CategoryList,
  DotSeparator,
  DraftFlag,
  FeaturedFlag,
  FormatDate,
  ItemAction,
  ItemTitle,
  ItemUserShape,
  ItemView,
  PrivacyIcon,
  SponsorFlag,
  Statistic,
  UserAvatar,
  LineIcon
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, styled, Typography } from '@mui/material';
import ProfileLink from '@metafox/feed/components/FeedItemView/ProfileLink';
import { useSelector } from 'react-redux';
import React from 'react';

const name = 'BlogDetailView';

const ContentWrapper = styled('div', { name, slot: 'ContentWrapper' })(
  ({ theme }) => ({
    backgroundColor: theme.mixins.backgroundColor('paper')
  })
);
const BgCover = styled('div', {
  name,
  slot: 'bgCover',
  shouldForwardProp: prop => prop !== 'isModalView'
})<{ isModalView: boolean }>(({ theme, isModalView }) => ({
  backgroundRepeat: 'no-repeat',
  backgroundPosition: 'center',
  backgroundSize: 'cover',
  height: 320,
  ...(isModalView && {
    marginLeft: theme.spacing(-2),
    marginRight: theme.spacing(-2)
  }),
  [theme.breakpoints.down('sm')]: {
    height: 179
  }
}));
const BlogViewContainer = styled('div', { name, slot: 'blogViewContainer' })(
  ({ theme }) => ({
    width: '100%',
    marginLeft: 'auto',
    marginRight: 'auto',
    backgroundColor: theme.mixins.backgroundColor('paper'),
    padding: theme.spacing(2),
    position: 'relative',
    borderBottomLeftRadius: theme.shape.borderRadius,
    borderBottomRightRadius: theme.shape.borderRadius
  })
);
const AvatarWrapper = styled('div', { name, slot: 'AvatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1.5)
  })
);
const BlogContent = styled('div', { name, slot: 'blogContent' })(
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

const HeadlineSpan = styled('span', { name: 'HeadlineSpan' })(({ theme }) => ({
  paddingRight: theme.spacing(0.5),
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

export function LoadingSkeleton({ wrapAs, wrapProps }) {
  return <ItemView testid="skeleton" wrapAs={wrapAs} wrapProps={wrapProps} />;
}

export function DetailView({
  user,
  identity,
  item,
  state,
  actions,
  handleAction,
  isModalView
}: ItemProps) {
  const {
    ItemActionMenu,
    ItemDetailInteraction,
    useGetItems,
    i18n,
    jsxBackend,
    assetUrl
  } = useGlobal();
  const categories = useGetItems<{ id: number; name: string }>(
    item?.categories
  );
  const owner = useSelector((state: GlobalState) =>
    getItemSelector(state, item?.owner)
  );

  const attachments = useGetItems(item?.attachments);
  const PendingCard = jsxBackend.get('core.itemView.pendingReviewCard');

  if (!user || !item) return null;

  const cover = getImageSrc(item?.image, '500', assetUrl('blog.no_image'));

  const { tags } = item;

  return (
    <Block testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <ContentWrapper>
          {cover ? (
            <BgCover
              isModalView={isModalView}
              style={{ backgroundImage: `url(${cover})` }}
            ></BgCover>
          ) : null}
          {PendingCard && (
            <Box sx={{ margin: 2 }}>
              <PendingCard sx item={item} />
            </Box>
          )}
          <BlogViewContainer>
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
            <CategoryList
              to="/blog/category"
              data={categories}
              sx={{ mb: 1, mr: 2 }}
            />
            <ItemTitle variant="h3" component={'div'} pr={2} showFull>
              <FeaturedFlag variant="itemView" value={item.is_featured} />
              <SponsorFlag variant="itemView" value={item.is_sponsor} />
              <DraftFlag
                value={item.is_draft}
                variant="h3"
                component="span"
                sx={{
                  verticalAlign: 'middle',
                  fontWeight: 'normal'
                }}
              />
              <Typography
                component="h1"
                variant="h3"
                sx={{
                  pr: 2.5,
                  display: { sm: 'inline', xs: 'block' },
                  mt: { sm: 0, xs: 1 },
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
                <ProfileLinkStyled
                  to={user.link}
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
                  <Statistic
                    values={item.statistic}
                    display={'total_view'}
                    component={'span'}
                    skipZero={false}
                  />
                  <PrivacyIcon
                    value={item?.privacy}
                    item={item?.privacy_detail}
                  />
                </DotSeparator>
              </Box>
            </Box>
            <BlogContent>
              <HtmlViewer html={item?.text || ''} />
            </BlogContent>
            {tags?.length > 0 ? (
              <Box mt={4} display="flex" flexWrap="wrap">
                {tags.map(tag => (
                  <TagItem key={tag}>
                    <Link to={`/blog/search?q=%23${encodeURIComponent(tag)}`}>
                      {tag}
                    </Link>
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
                  {attachments.map(item => (
                    <AttachmentItemWrapper key={item.id.toString()}>
                      <AttachmentItem
                        fileName={item.file_name}
                        downloadUrl={item.download_url}
                        isImage={item.is_image}
                        fileSizeText={item.file_size_text}
                        size="large"
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
            />
          </BlogViewContainer>
        </ContentWrapper>
      </BlockContent>
    </Block>
  );
}

DetailView.LoadingSkeleton = LoadingSkeleton;
DetailView.displayName = 'BlogItem_DetailView';

const Enhance = connectSubject(
  connectItemView(DetailView, actionCreators, {
    categories: true,
    attachments: true
  })
);

export default createBlock<Props>({
  extendBlock: Enhance
});
