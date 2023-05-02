/**
 * @type: ui
 * name: livestreaming.ui.liveVideoPlayer
 */
import { useGlobal, useFirestoreDocIdListener } from '@metafox/framework';
import * as React from 'react';
import { Box, styled, Button } from '@mui/material';
import { getImageSrc } from '@metafox/utils';
import { LivestreamItemShape } from '@metafox/livestreaming/types';
import VideoPlayer from '@metafox/ui/VideoPlayer';
import { LineIcon } from '@metafox/ui';

type Props = {
  item: LivestreamItemShape;
  dialog?: boolean;
  actions?: Record<string, any>;
  dashboard?: boolean;
};

const EndPlayer = styled(Box, {
  name: 'EndPlayer',
  shouldForwardProp: (prop: string) => prop !== 'dialog'
})<{ dialog: boolean }>(({ theme, dialog }) => ({
  position: 'relative',
  backgroundColor: '#333',
  color: '#fff',
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  justifyContent: 'center',
  fontSize: '24px',
  ...(dialog && {
    width: '100%',
    height: '100%'
  }),
  '&:before': {
    content: '""',
    display: 'block',
    paddingBottom: '56.25%'
  }
}));

const EndPlayerContent = styled(Box, {
  name: 'EndPlayer',
  shouldForwardProp: (prop: string) => prop !== 'dialog'
})<{ dialog: boolean }>(({ theme }) => ({
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'center',
  justifyContent: 'center',
  position: 'absolute',
  top: 0,
  left: 0,
  right: 0,
  bottom: 0
}));

const WrapperButtons = styled(Box)(({ theme }) => ({
  display: 'flex',
  justifyContent: 'center',
  alignItems: 'center',
  '& > *': {
    marginRight: `${theme.spacing(1)} !important`
  }
}));
type LiveProps = {
  time_limit_warning?: boolean;
  status: string;
  stream_key: string;
  end_date?: string;
};

function LiveVideoPlayer({ item, dialog, actions, dashboard }: Props) {
  const { firebaseBackend, i18n, dialogBackend, moment } = useGlobal();
  const db = firebaseBackend.getFirestore();
  const dataLive = useFirestoreDocIdListener<LiveProps>(db, {
    collection: 'live_video',
    docID: item?.stream_key
  });

  const handleDeleteLiveVideo = React.useCallback(() => {
    actions.deleteItem();
  }, []);
  const { video_url, thumbnail_url, is_owner, is_streaming } = item || {};
  const offline = dataLive?.status === 'idle' || !is_streaming;
  const showWarningLimitTime =
    is_streaming && is_owner && dashboard && dataLive?.time_limit_warning;
  const minuteRemain = dataLive?.end_date
    ? moment(dataLive?.end_date).diff(new Date(), 'minutes')
    : '';

  React.useEffect(() => {
    if (offline && actions) {
      actions.updateStatusOffline();
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [offline, actions]);

  React.useEffect(() => {
    if (showWarningLimitTime && minuteRemain > 0) {
      dialogBackend.alert({
        title: i18n.formatMessage({ id: 'timeout' }),
        message: i18n.formatMessage(
          { id: 'the_live_video_will_end_in_n_minutes' },
          {
            value: minuteRemain
          }
        )
      });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [showWarningLimitTime, minuteRemain]);

  if (!item) return null;

  const cover = getImageSrc(thumbnail_url, '500', '');

  if (offline) {
    return (
      <EndPlayer dialog={dialog}>
        <EndPlayerContent>
          <LineIcon icon="ico-video" sx={{ fontSize: 40, mb: 2 }} />
          {i18n.formatMessage({
            id: is_owner ? 'your_live_video_has_ended' : 'live_video_had_ended'
          })}
          {is_owner ? (
            <WrapperButtons mt={2}>
              <Button
                data-testid="buttonDeleteLiveVideo"
                role="button"
                tabIndex={1}
                autoFocus
                variant="contained"
                disableRipple
                size="medium"
                color="error"
                onClick={handleDeleteLiveVideo}
                sx={{ minWidth: 100 }}
              >
                {i18n.formatMessage({ id: 'delete' })}
              </Button>
              <Button
                data-testid="buttonViewLiveVideo"
                role="button"
                tabIndex={2}
                variant="contained"
                disableRipple
                size="medium"
                color="primary"
                href={item.link || `/live-video/${item?.id}`}
                sx={{ minWidth: 100 }}
              >
                {i18n.formatMessage({ id: 'view' })}
              </Button>
            </WrapperButtons>
          ) : null}
        </EndPlayerContent>
      </EndPlayer>
    );
  }

  return <VideoPlayer src={video_url} thumb_url={cover} autoPlay />;
}

export default LiveVideoPlayer;
