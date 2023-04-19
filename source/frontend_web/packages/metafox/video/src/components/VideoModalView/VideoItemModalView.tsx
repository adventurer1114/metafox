/**
 * @type: ui
 * name: video.itemView.modalCard
 */
import { useGlobal, useLoggedIn } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { VideoItemShape } from '@metafox/video';
import VideoPlayer from '@metafox/ui/VideoPlayer';
import { IconButton, styled, Tooltip, Box } from '@mui/material';
import * as React from 'react';

type VideoItemModalViewProps = {
  item: VideoItemShape;
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
  item,
  onMinimizePhoto
}: VideoItemModalViewProps) {
  const { i18n, useDialog, useIsMobile } = useGlobal();
  const { closeDialog } = useDialog();
  const loggedIn = useLoggedIn();

  const [minimize, setMinimize] = React.useState<boolean>(true);
  const isMobile = useIsMobile();

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

  return (
    <Box sx={{ height: '100%' }}>
      <VideoPlayer
        src={item.video_url || item.destination || null}
        thumb_url={item.image}
        autoPlay
      />
      <ActionBar>
        {!isMobile ? (
          <Box>
            <Tooltip title={i18n.formatMessage({ id: 'close' })}>
              <IconButtonModal onClick={handleClose}>
                <LineIcon icon="ico-close" color="white" />
              </IconButtonModal>
            </Tooltip>
          </Box>
        ) : null}
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
