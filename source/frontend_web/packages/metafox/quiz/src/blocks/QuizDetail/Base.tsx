import {
  GlobalState,
  Link,
  useGlobal,
  getItemSelector
} from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import { getQuizResultSelector } from '@metafox/quiz/selectors/quizResultSelector';
import {
  AttachmentItem,
  DotSeparator,
  FeaturedFlag,
  FormatDate,
  ItemTitle,
  PrivacyIcon,
  SponsorFlag,
  UserAvatar,
  LineIcon,
  Statistic
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, Button, Typography, styled } from '@mui/material';
import React from 'react';
import { useSelector } from 'react-redux';
import QuizQuestion from './QuizQuestion';
import useStyles from './styles';
import ProfileLink from '@metafox/feed/components/FeedItemView/ProfileLink';

const HeadlineSpan = styled('span', { name: 'HeadlineSpan' })(({ theme }) => ({
  paddingRight: theme.spacing(0.5),
  color: theme.palette.text.secondary
}));

const ProfileLinkStyled = styled(Link, {
  name: 'ProfileLinkStyled'
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

const StatisticStyled = styled(Statistic, {
  name: 'QuizDetail',
  slot: 'Statistic'
})(({ theme }) => ({
  '&:before': {
    color: theme.palette.text.secondary,
    content: '"·"',
    paddingLeft: '0.25em',
    paddingRight: '0.25em'
  }
}));

const PlayStyled = styled(Box, {
  name: 'QuizDetail',
  slot: 'btnPlay',
  shouldForwardProp: props => props !== 'isCanViewVoteAnswer'
})<{ isCanViewVoteAnswer?: boolean }>(({ theme, isCanViewVoteAnswer }) => ({
  display: 'inline-block',
  ...(isCanViewVoteAnswer && {
    fontSize: theme.mixins.pxToRem(13),
    color: theme.palette.primary.main,
    cursor: 'pointer',
    '&:hover': {
      textDecoration: 'underline'
    }
  })
}));

export default function QuizDetail({
  item,
  user,
  questions,
  actions,
  blockProps,
  identity,
  attachments,
  handleAction,
  state
}) {
  const classes = useStyles();
  const {
    ItemActionMenu,
    ItemDetailInteraction,
    i18n,
    dispatch,
    assetUrl,
    useSession,
    jsxBackend,
    dialogBackend,
    useGetItems,
    useGetItem
  } = useGlobal();
  const { user: authUser } = useSession();

  const [resultSubmit, setQuizResult] = React.useState<Record<string, number>>(
    {}
  );
  const results = useSelector<GlobalState>(state =>
    getQuizResultSelector(state, item?.results)
  ) as Record<string, any>;

  const memberResults = useGetItems(item?.member_results);

  const userOwner = useGetItem(item?.user);

  const owner = useSelector((state: GlobalState) =>
    getItemSelector(state, item?.owner)
  );

  const PendingCard = jsxBackend.get('core.itemView.pendingReviewCard');

  if (!item || !user) return null;

  const {
    statistic,
    id: quizId,
    is_featured,
    is_sponsor,
    is_pending,
    extra
  } = item;

  const handleSubmit = () => {
    if (!authUser) {
      dispatch({
        type: 'user/showDialogLogin'
      });

      return;
    }

    dispatch({
      type: 'submitQuiz',
      payload: {
        quiz_id: quizId,
        answers: resultSubmit
      }
    });
  };

  const cover = getImageSrc(
    item.image,
    '1024',
    assetUrl('quiz.cover_no_image')
  );

  const handleSetQuiz = value => {
    if (!authUser) {
      dispatch({
        type: 'user/showDialogLogin'
      });

      return;
    }

    setQuizResult(value);
  };

  const openDialogPlayed = () => {
    if (!isCanViewVoteAnswer) return;

    dialogBackend.present({
      component: 'quiz.dialog.PeoplePlayed',
      props: {
        dialogTitle: 'people_played_this',
        questions,
        user: memberResults,
        userOwner,
        item
      }
    });
  };

  const canPlay = !results && !is_pending && extra?.can_play;

  const isCanViewVoteAnswer =
    Boolean(statistic?.total_play) &&
    (results ? true : item?.extra?.can_view_results_before_answer);

  return (
    <Block testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <div className={classes.root}>
          <div
            className={classes.bgCover}
            style={{ backgroundImage: `url(${cover})` }}
          ></div>
          {PendingCard && (
            <Box sx={{ margin: 2 }}>
              <PendingCard sx item={item} />
            </Box>
          )}
          <div className={classes.viewContainer}>
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
                  {item?.title}
                </Typography>
              </ItemTitle>
              <div className={classes.author}>
                <Box component="div">
                  <UserAvatar user={user} size={48} />
                </Box>
                <div className={classes.authorInfo}>
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
                  <DotSeparator sx={{ color: 'text.secondary', mt: 0.5 }}>
                    <FormatDate
                      data-testid="creationDate"
                      value={item.creation_date}
                      format="MMMM DD, yyyy"
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
                  <HtmlViewer html={item.text} />
                </Box>
              )}
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
              {questions?.length > 0 &&
                questions.map((i, index) => (
                  <QuizQuestion
                    key={index.toString()}
                    question={i.question}
                    questionId={i.id}
                    answers={i.answers}
                    order={index + 1}
                    setQuizResult={handleSetQuiz}
                    disabled={!canPlay}
                    result={results?.user_result?.find(
                      item => item.question_id === i.id
                    )}
                  />
                ))}
              {canPlay && (
                <Button
                  variant="contained"
                  color="primary"
                  className={classes.button}
                  onClick={handleSubmit}
                >
                  {i18n.formatMessage({ id: 'submit' })}
                </Button>
              )}
              <div className={classes.result}>
                {results &&
                  i18n.formatMessage(
                    { id: 'you_have_correct_answer' },
                    {
                      result: () => <strong>{results?.result_correct}</strong>
                    }
                  )}

                <div className={classes.count}>
                  <PlayStyled
                    isCanViewVoteAnswer={isCanViewVoteAnswer}
                    onClick={openDialogPlayed}
                  >
                    {i18n.formatMessage(
                      { id: 'total_play' },
                      { value: statistic?.total_play || 0 }
                    )}
                  </PlayStyled>
                  <StatisticStyled
                    values={statistic}
                    display={'total_view'}
                    component={'span'}
                    skipZero={false}
                    color="text.secondary"
                    variant="body2"
                    fontWeight="fontWeightBold"
                  />
                </div>
              </div>
              <ItemDetailInteraction
                identity={identity}
                state={state}
                handleAction={handleAction}
              />
            </div>
          </div>
        </div>
      </BlockContent>
    </Block>
  );
}
