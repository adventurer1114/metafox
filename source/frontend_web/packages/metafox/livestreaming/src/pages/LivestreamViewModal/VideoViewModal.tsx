/**
 * @type: dialog
 * name: livestreaming.dialog.videoView
 */

import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { connectItemView, useGlobal } from '@metafox/framework';
import { Box, styled } from '@mui/material';
import * as React from 'react';
import { LivestreamDetailViewProps } from '../../types';
import ErrorBoundary from '@metafox/core/pages/ErrorPage/Page';
import actionCreators from '@metafox/livestreaming/actions/livestreamItemActions';

const name = 'videoView';

const DialogVideo = styled('div', { name: 'VideoView', slot: 'dialogVideo' })(
  ({ theme }) => ({
    position: 'relative',
    backgroundColor: '#000',
    width: '100%',
    overflow: 'hidden',
    '& iframe': {
      width: '100%',
      height: '100%'
    },
    [theme.breakpoints.down('md')]: {
      width: '65%'
    },
    [theme.breakpoints.down('sm')]: {
      width: '100%',
      height: 'auto',
      borderRadius: 0,
      overflow: 'initial'
    }
  })
);

const DialogStatistic = styled('div', {
  name: 'VideoView',
  slot: 'dialogStatistic',
  shouldForwardProp: prop => prop !== 'isExpand'
})<{
  isExpand: boolean;
}>(({ theme, isExpand }) => ({
  height: '100%',
  width: isExpand ? 0 : '480px',
  flexGrow: 1,
  [theme.breakpoints.down('md')]: {
    width: '35%'
  },
  [theme.breakpoints.down('sm')]: {
    width: '100%'
  },
  [theme.breakpoints.down('xs')]: {
    width: '100%',
    height: '400px'
  }
}));

const Root = styled(DialogContent, {
  name: 'VideoView',
  slot: 'dialogStatistic'
})<{}>(({ theme }) => ({
  padding: '0 !important',
  height: '100%',
  display: 'flex',
  overflowX: 'hidden',
  [theme.breakpoints.down('sm')]: {
    flexFlow: 'column'
  }
}));

const StyledWrapperStatistic = styled(Box, { name, slot: 'WrapperStatistic' })(
  ({ theme }) => ({
    display: 'flex',
    flexDirection: 'column',
    height: '100%'
  })
);

function LiveVideoViewDialog({
  item,
  identity,
  error,
  actions,
  user
}: LivestreamDetailViewProps) {
  const {
    ItemDetailInteractionInModal,
    useDialog,
    useIsMobile,
    i18n,
    jsxBackend,
    useSession
  } = useGlobal();
  const { dialogProps } = useDialog();
  const isMobile = useIsMobile();
  const [isExpand, setExpand] = React.useState<boolean>(false);
  const refViewed = React.useRef(false);
  const { user: authUser, loggedIn } = useSession();
  const isOwner = authUser?.id === user?.id;
  const [parentReply, setParentReply] = React.useState<
    Record<string, any> | undefined
  >();

  const VideoItemModalView = jsxBackend.get('livestreaming.itemView.modalCard');
  const ListingComment = jsxBackend.get(
    'livestreaming.block.commentLiveListing'
  );

  React.useEffect(() => {
    if (refViewed.current || !item?.is_streaming || isOwner || !loggedIn)
      return;

    // update viewer
    refViewed.current = true;
    actions.updateViewer();

    return () => {
      actions.removeViewer();
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [item?.is_streaming]);

  if (!item) return null;

  const onMinimizePhoto = (minimize: boolean) => {
    setExpand(minimize);
  };

  const handleSuccess = () => {
    setParentReply(undefined);
  };

  const removeReply = () => {
    setParentReply(undefined);
  };

  const { is_streaming } = item;
  const startFooterItems = is_streaming
    ? [
        {
          component: 'livestreaming.ui.watchingUsers',
          props: {
            streamKey: item?.stream_key
          }
        },
        {
          component: 'livestreaming.ui.composerReplyInfo',
          props: {
            item: parentReply,
            removeReply,
            sx: { mt: 1 }
          }
        }
      ]
    : undefined;

  return (
    <Dialog
      scroll={'body'}
      {...dialogProps}
      fullScreen={!error}
      data-testid="popupDetailVideo"
      onBackdropClick={undefined}
    >
      {isMobile || error ? (
        <DialogTitle enableBack={!error} disableClose={isMobile}>
          {i18n.formatMessage({ id: 'video' })}
        </DialogTitle>
      ) : null}
      <ErrorBoundary error={error}>
        <Root>
          <DialogVideo>
            {VideoItemModalView ? (
              <VideoItemModalView
                item={item}
                onMinimizePhoto={onMinimizePhoto}
                identity={identity}
                actions={actions}
              />
            ) : null}
          </DialogVideo>
          <DialogStatistic isExpand={isExpand}>
            <StyledWrapperStatistic
              sx={{
                display: isExpand ? 'none' : 'flex',
                flexDirection: 'column'
              }}
            >
              <Box sx={{ flex: 1, minHeight: 0 }}>
                <ItemDetailInteractionInModal
                  identity={identity}
                  startFooterItems={startFooterItems}
                  statisticDisplay={!is_streaming ? 'total_view' : ''}
                  commentComposerProps={{
                    onSuccess: handleSuccess,
                    identity: parentReply?.parent_comment_identity || identity
                  }}
                  commentlistingComponent={
                    is_streaming ? (
                      <ListingComment
                        identity={identity}
                        streamKey={item?.stream_key}
                        setParentReply={setParentReply}
                      />
                    ) : undefined
                  }
                />
              </Box>
            </StyledWrapperStatistic>
          </DialogStatistic>
        </Root>
      </ErrorBoundary>
    </Dialog>
  );
}

export default connectItemView(LiveVideoViewDialog, actionCreators);
