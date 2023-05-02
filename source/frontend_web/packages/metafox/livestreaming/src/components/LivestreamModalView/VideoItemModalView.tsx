/**
 * @type: ui
 * name: livestreaming.itemView.modalCard
 */
import { useGlobal, useLoggedIn } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { LivestreamItemShape } from '@metafox/livestreaming/types';
import VideoPlayer from '@metafox/ui/VideoPlayer';
import { IconButton, styled, Tooltip, Box } from '@mui/material';
import * as React from 'react';
import { getImageSrc } from '@metafox/utils';

type LiveItemModalViewProps = {
  item: LivestreamItemShape;
  hideActionMenu?: boolean;
  onMinimizePhoto: (minimize: boolean) => void;
};

const name = 'VideoItemModalView';

const ActionBar = styled('div', { name, slot: 'actionBar' })(({ theme }) => ({
  position: 'absolute',
  right: 0,
  top: 0,
  width: '100%',
  padding: theme.spacing(1),
  display: 'flex',
  justifyContent: 'space-between',
  zIndex: 1,
  alignItems: 'center'
}));

const IconButtonModal = styled(IconButton, { name, slot: 'tagFriend' })(
  ({ theme }) => ({
    color: '#fff !important',
    width: 32,
    height: 32,
    fontSize: theme.mixins.pxToRem(15)
  })
);

export default function VideoItemModalView({
  identity,
  item,
  onMinimizePhoto,
  actions
}: LiveItemModalViewProps) {
  const { i18n, useDialog, useIsMobile, jsxBackend } = useGlobal();
  const { closeDialog } = useDialog();
  const loggedIn = useLoggedIn();

  const [minimize, setMinimize] = React.useState<boolean>(true);
  const isMobile = useIsMobile();
  const LiveLabel = jsxBackend.get('livestreaming.ui.labelLive');
  const FlyReaction = jsxBackend.get('livestreaming.ui.flyReaction');
  const ViewerLabel = jsxBackend.get('livestreaming.ui.labelViewer');
  const LiveVideoPlayer = jsxBackend.get('livestreaming.ui.liveVideoPlayer');

  if (!item) return null;

  const handleFullSize = () => {
    const minimizeItem = minimize;
    setMinimize(!minimizeItem);
    onMinimizePhoto && onMinimizePhoto(minimizeItem);
  };

  const handleClose = () => {
    closeDialog();
    onMinimizePhoto && onMinimizePhoto(false);
  };

  const { is_streaming, stream_key, thumbnail_url, _live_watching } =
    item || {};
  const cover = getImageSrc(thumbnail_url, '500', '');

  return (
    <Box sx={{ height: '100%', position: 'relative' }}>
      {is_streaming || _live_watching ? (
        <LiveVideoPlayer item={item} dialog actions={actions} />
      ) : (
        <VideoPlayer
          src={item.video_url || item.destination || null}
          thumb_url={cover}
          autoPlay
        />
      )}
      {FlyReaction ? (
        <FlyReaction streamKey={item?.stream_key} identity={identity} />
      ) : null}
      <ActionBar>
        <Box sx={{ display: 'inline-flex', alignItems: 'center' }}>
          {!isMobile ? (
            <Tooltip title={i18n.formatMessage({ id: 'close' })}>
              <IconButtonModal onClick={handleClose}>
                <LineIcon icon="ico-close" color="white" />
              </IconButtonModal>
            </Tooltip>
          ) : null}
          {is_streaming && LiveLabel ? (
            <Box mx={1}>
              <LiveLabel />
            </Box>
          ) : null}
          {is_streaming && ViewerLabel ? (
            <ViewerLabel streamKey={stream_key} />
          ) : null}
        </Box>
        {loggedIn ? (
          <Box sx={{ marginLeft: 'auto' }}>
            <Tooltip
              title={i18n.formatMessage({
                id: minimize ? 'switch_to_full_screen' : 'exit_full_screen'
              })}
            >
              <IconButtonModal onClick={handleFullSize}>
                <LineIcon
                  icon={minimize ? 'ico-arrow-expand' : 'ico-arrow-collapse'}
                  color="white"
                />
              </IconButtonModal>
            </Tooltip>
          </Box>
        ) : null}
      </ActionBar>
    </Box>
  );
}
