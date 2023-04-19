import {
  Link,
  useGlobal,
  getItemSelector,
  GlobalState
} from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import { PollDetailViewProps } from '@metafox/poll/types';
import {
  AttachmentItem,
  DotSeparator,
  FeaturedFlag,
  FormatDate,
  ItemTitle,
  PrivacyIcon,
  RichTextViewMore,
  SponsorFlag,
  Statistic,
  UserAvatar,
  LineIcon
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, styled, Typography } from '@mui/material';
import React from 'react';
import PollVoteForm from './PollVoteForm';
import useStyles from './styles';
import { useSelector } from 'react-redux';
import ProfileLink from '@metafox/feed/components/FeedItemView/ProfileLink';

const name = 'PollDetail';
const ContentWrapper = styled('div', { name, slot: 'ContentWrapper' })(
  ({ theme }) => ({
    backgroundColor: theme.mixins.backgroundColor('paper'),
    [theme.breakpoints.down('sm')]: {
      '& $bgCover': {
        height: 179
      },
      '& $viewContainer': {
        borderRadius: 0,
        marginTop: '0 !important'
      }
    }
  })
);

const MessageWrapper = styled('div', { name, slot: 'MessageWrapper' })(
  ({ theme }) => ({
    borderRadius: 8,
    height: theme.spacing(6),
    width: 'auto',
    backgroundColor: theme.palette.action.hover,
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    padding: theme.spacing(0, 2),
    marginBottom: theme.spacing(2)
  })
);

const BgCover = styled('div', {
  name,
  slot: 'bgCover'
})(({ theme }) => ({
  backgroundRepeat: 'no-repeat',
  backgroundPosition: 'center',
  backgroundSize: 'cover',
  height: 320
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

export default function PollDetail({
  item,
  user,
  attachments,
  actions,
  answers,
  identity,
  handleAction,
  state
}: PollDetailViewProps) {
  const classes = useStyles();
  const { ItemActionMenu, ItemDetailInteraction, i18n, jsxBackend, assetUrl } =
    useGlobal();
  const owner = useSelector((state: GlobalState) =>
    getItemSelector(state, item?.owner)
  );

  const PendingCard = jsxBackend.get('core.itemView.pendingReviewCard');

  if (!item || !user) return null;

  const {
    item_id,
    is_user_voted,
    is_multiple,
    public_vote,
    is_featured,
    is_sponsor,
    is_pending,
    is_closed,
    extra
  } = item;

  const cover = getImageSrc(item?.image, '500', assetUrl('poll.no_image'));

  return (
    <Block testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <ContentWrapper>
          {cover && <BgCover style={{ backgroundImage: `url(${cover})` }} />}
          <div className={classes.viewContainer}>
            {PendingCard && (
              <Box sx={{ marginBottom: 2 }}>
                <PendingCard sx item={item} />
              </Box>
            )}
            {is_closed && (
              <MessageWrapper>
                <Typography variant="h5" color="text.hint">
                  {i18n.formatMessage({ id: 'voting_for_the_poll_was_closed' })}
                </Typography>
              </MessageWrapper>
            )}
            <div className={classes.contentWrapper}>
              <div className={classes.actionMenu}>
                <ItemActionMenu
                  identity={identity}
                  icon={'ico-dottedmore-vertical-o'}
                  state={state}
                  handleAction={handleAction}
                />
              </div>
              <ItemTitle variant="h3" component={'div'} showFull>
                <FeaturedFlag variant="itemView" value={is_featured} />
                <SponsorFlag variant="itemView" value={is_sponsor} />
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
                  {item?.question}
                </Typography>
              </ItemTitle>
              <div className={classes.author}>
                <div>
                  <UserAvatar user={user} size={48} />
                </div>
                <div className={classes.authorInfo}>
                  {user ? (
                    <ProfileLinkStyled
                      to={user.link}
                      children={user.full_name}
                      hoverCard={`/user/${user.id}`}
                      data-testid="headline"
                    />
                  ) : null}
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
                  <DotSeparator sx={{ color: 'text.secondary', mt: 0.5 }}>
                    <FormatDate
                      value={item.creation_date}
                      format="MMMM DD, yyyy"
                      data-testid="creationDate"
                    />
                    <Statistic
                      values={item?.statistic}
                      display={'total_view'}
                      component={'span'}
                      skipZero={false}
                    />
                    <PrivacyIcon
                      value={item?.privacy}
                      item={item?.privacy_detail}
                    />
                  </DotSeparator>
                </div>
              </div>
              {item?.text && (
                <Box component="div" mt={3} className={classes.itemContent}>
                  <RichTextViewMore maxHeight="300px">
                    <HtmlViewer html={item.text || ''} />
                  </RichTextViewMore>
                </Box>
              )}
              <div className={classes.voteForm}>
                <PollVoteForm
                  isMultiple={is_multiple}
                  isVoted={is_user_voted}
                  pollId={item_id}
                  answers={answers}
                  statistic={item.statistic}
                  closeTime={item.close_time}
                  publicVote={public_vote}
                  identity={identity}
                  isPending={is_pending}
                  isClosed={is_closed}
                  canVoteAgain={extra.can_change_vote}
                  canVote={extra.can_vote}
                  canViewResult={extra.can_view_result}
                  canViewResultAfter={extra.can_view_result_after_vote}
                  canViewResultBefore={extra.can_view_result_before_vote}
                />
              </div>
              {attachments?.length > 0 && (
                <>
                  <div className={classes.attachmentTitle}>
                    {i18n.formatMessage({ id: 'attachments' })}
                  </div>
                  <div className={classes.attachment}>
                    {attachments.map(item => (
                      <div
                        className={classes.attachmentItem}
                        key={item.id.toString()}
                      >
                        <AttachmentItem
                          fileName={item.file_name}
                          downloadUrl={item.download_url}
                          isImage={item.is_image}
                          fileSizeText={item.file_size_text}
                          image={item?.image}
                          size="large"
                        />
                      </div>
                    ))}
                  </div>
                </>
              )}
              <ItemDetailInteraction
                identity={identity}
                state={state}
                handleAction={handleAction}
              />
            </div>
          </div>
        </ContentWrapper>
      </BlockContent>
    </Block>
  );
}
