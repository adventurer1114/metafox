/**
 * @type: dialog
 * name: comment.dialog.commentList
 */

import { Dialog, DialogContent, DialogTitle, useDialog } from '@metafox/dialog';
import { getItemSelector, GlobalState, useGlobal } from '@metafox/framework';
import { FeedStatistic, LineIcon } from '@metafox/ui';
import { Button, Divider, styled, Typography } from '@mui/material';
import React, { useEffect, useRef, useState } from 'react';
import { useSelector } from 'react-redux';
import { useSortComment } from '@metafox/comment/hooks';

const name = 'CommentDialog';

const ButtonBack = styled(Button, { name, slot: 'btnBack' })(({ theme }) => ({
  color: `${theme.palette.text.hint} !important`,
  [theme.breakpoints.down('sm')]: {
    paddingRight: `${theme.spacing(2.5)} !important`
  }
}));
const DialogContentStyled = styled(DialogContent, {
  name,
  slot: 'DialogContentStyled'
})(({ theme }) => ({
  borderRadius: theme.shape.borderRadius,
  backgroundColor: theme.palette.background.paper,
  padding: theme.spacing(0, 2),
  paddingBottom: 0,
  [theme.breakpoints.down('sm')]: {
    borderRadius: 0
  }
}));
const ActionButtonStaticsWrapper = styled(DialogContent, {
  name,
  slot: 'actionButtonStaticsWrapper'
})(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  borderBottom: 'solid 1px',
  borderBottomColor: theme.palette.border?.secondary,
  padding: 0,
  [theme.breakpoints.down('sm')]: {
    flexDirection: 'column-reverse'
  }
}));
const ReactionWrapper = styled(DialogContent, {
  name,
  slot: 'reactionWrapper'
})(({ theme }) => ({
  padding: 0,
  '& .MuiFeedCommentBlock-itemOuter': {
    border: 'none'
  },
  [theme.breakpoints.down('sm')]: {
    width: '100%'
  }
}));
const FeedStatisticWrapper = styled(DialogContent, {
  name,
  slot: 'FeedStatisticWrapper'
})(({ theme }) => ({
  flex: 1,
  display: 'flex',
  justifyContent: 'flex-end',
  padding: '8px 0',
  [theme.breakpoints.down('sm')]: {
    width: '100%',
    fontWeight: theme.typography.fontWeightBold,
    borderBottom: 'solid 1px',
    borderBottomColor: theme.palette.border?.secondary
  }
}));

export default function CommentDialog({
  identity,
  handleAction,
  isFocus,
  viewMoreComments
}: any) {
  const statisticRef = useRef(null);
  const composerRef = useRef(null);
  const titleRef = useRef(null);
  const contentRef = useRef(null);

  const [height, setHeight] = useState(0);
  const { dialogProps, closeDialog } = useDialog();
  const {
    i18n,
    CommentList,
    CommentActButton,
    ShareActButton,
    ReactionActButton,
    CommentReaction,
    useSession,
    useTheme,
    jsxBackend
  } = useGlobal();
  const theme = useTheme();
  const session = useSession();
  const [sortType, setSortType] = useSortComment();

  const CommentComposer = jsxBackend.get('CommentComposer');

  const item = useSelector<GlobalState>(state =>
    getItemSelector(state, identity)
  ) as any;

  useEffect(() => {
    setImmediate(() => {
      handleChangeHeight();
    });

    window.addEventListener('resize', handleChangeHeight);

    return () => {
      window.removeEventListener('resize', handleChangeHeight);
    };
  }, []);

  const handleChangeHeight = () => {
    const composerHeight = composerRef.current?.offsetHeight;
    const statisticHeight = statisticRef.current?.offsetHeight;
    const titleHeight = titleRef.current?.offsetHeight;
    const contentHeight = contentRef.current?.offsetHeight;

    const style = getComputedStyle(contentRef.current);
    const paddingTop = Number(
      style.getPropertyValue('padding-top').replace(/\D/g, '')
    );
    const paddingBottom = Number(
      style.getPropertyValue('padding-bottom').replace(/\D/g, '')
    );

    const commentsHeight =
      contentHeight -
      paddingTop -
      paddingBottom -
      (composerHeight + statisticHeight);

    setHeight(
      window.innerHeight - titleHeight - (contentHeight - commentsHeight)
    );
  };

  const { statistic, most_reactions, like_phrase } = item as any;

  return (
    <Dialog {...dialogProps} scroll="body">
      <DialogTitle ref={titleRef} disableClose>
        <div>
          <ButtonBack
            onClick={closeDialog}
            sx={{ minWidth: 0, paddingRight: theme.spacing(2.5) }}
            color="secondary"
            size="medium"
          >
            <LineIcon icon={'ico-angle-left'} />
          </ButtonBack>
          <Typography component="span" fontWeight="bold">
            {i18n.formatMessage({ id: 'comments' })}
          </Typography>
        </div>
      </DialogTitle>
      <DialogContentStyled ref={contentRef}>
        <>
          <ActionButtonStaticsWrapper ref={statisticRef}>
            <ReactionWrapper>
              <CommentReaction>
                {session.loggedIn &&
                item.extra?.can_like &&
                ReactionActButton ? (
                  <ReactionActButton
                    reacted={item.user_reacted}
                    identity={identity}
                    handleAction={handleAction}
                  />
                ) : null}
                {session.loggedIn &&
                item.extra?.can_comment &&
                CommentActButton ? (
                  <CommentActButton
                    identity={identity}
                    handleAction={handleAction}
                  />
                ) : null}
                {session.loggedIn && item.extra.can_share && ShareActButton ? (
                  <ShareActButton
                    handleAction={handleAction}
                    identity={identity}
                  />
                ) : null}
              </CommentReaction>
            </ReactionWrapper>
            <FeedStatisticWrapper>
              <FeedStatistic
                handleAction={handleAction}
                identity={identity}
                reactions={most_reactions}
                message={like_phrase}
                statistic={statistic}
              />
            </FeedStatisticWrapper>
          </ActionButtonStaticsWrapper>
          <div style={{ height, overflowY: 'scroll' }}>
            <CommentList
              identity={identity}
              handleAction={handleAction}
              data={item.related_comments}
              total_hidden={item?.related_comments_statistic?.total_hidden}
              viewMoreComments={viewMoreComments}
              total_comment={statistic?.total_comment}
              total_reply={statistic?.total_reply}
              setSortType={setSortType}
              sortType={sortType}
            />
          </div>
          {session.loggedIn && item.extra.can_comment && CommentComposer ? (
            <div ref={composerRef}>
              <Divider sx={{ display: { xs: 'block', md: 'none' } }} />
              <CommentComposer identity={identity} open focus={isFocus} />
            </div>
          ) : null}
        </>
      </DialogContentStyled>
    </Dialog>
  );
}
