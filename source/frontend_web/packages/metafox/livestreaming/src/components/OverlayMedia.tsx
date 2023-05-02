/**
 * @type: ui
 * name: livestreaming.ui.overlayVideo
 */
import { Link, useGlobal } from '@metafox/framework';
import { useBlock } from '@metafox/layout';
import { Image, LineIcon } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { LivestreamItemProps } from '@metafox/livestreaming/types';
import { Box, styled } from '@mui/material';
import * as React from 'react';

const OverlayStyled = styled(Box, {
  shouldForwardProp: props => props !== 'isMobile'
})<{ isMobile?: boolean }>(({ theme, isMobile }) => ({
  position: 'absolute',
  top: 0,
  bottom: 0,
  left: 0,
  right: 0,
  backgroundColor: 'rgba(0,0,0,0.4)',
  border: theme.mixins.border('secondary'),
  opacity: 0,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  cursor: 'pointer',
  transition: 'all 300ms ease',

  '& .iconPlay': {
    color: '#fff',
    fontSize: 48,
    position: 'relative'
  },
  '&:hover': {
    opacity: 1
  },
  ...(isMobile && {
    opacity: 1
  })
}));

const OverlayMedia = ({ item }: LivestreamItemProps) => {
  const { useIsMobile, jsxBackend } = useGlobal();
  const { itemLinkProps } = useBlock();
  const LiveLabel = jsxBackend.get('livestreaming.ui.labelLive');
  const DurationTime = jsxBackend.get('livestreaming.ui.durationTime');
  const ViewerLabel = jsxBackend.get('livestreaming.ui.labelViewer');
  const isMobile = useIsMobile();

  if (!item) return null;

  const { link: to, is_streaming, duration, stream_key } = item;

  const cover = getImageSrc(item.thumbnail_url, '500', '');

  return (
    <Link to={to} asModal sx={{ position: 'relative' }} {...itemLinkProps}>
      <Image src={cover} aspectRatio={'169'} />
      <OverlayStyled isMobile={isMobile}>
        <LineIcon className="iconPlay" icon="ico-play-circle-o" />
      </OverlayStyled>
      {is_streaming ? (
        <Box sx={{ position: 'absolute', top: '8px', left: '8px' }}>
          {LiveLabel ? <LiveLabel mr={1} /> : null}
          {ViewerLabel ? <ViewerLabel streamKey={stream_key} /> : null}
        </Box>
      ) : (
        <Box sx={{ position: 'absolute', bottom: '8px', left: '8px' }}>
          <DurationTime time={duration} />
        </Box>
      )}
    </Link>
  );
};

export default OverlayMedia;
