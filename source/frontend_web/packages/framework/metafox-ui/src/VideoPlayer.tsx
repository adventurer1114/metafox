import { RouteLink, useGlobal } from '@metafox/framework';
import { Image, LineIcon } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { styled } from '@mui/material';
import React, { useEffect } from 'react';
import useStyles from './VideoPlayer.styles';
import loadable from '@loadable/component';
import { useInView } from 'react-intersection-observer';
import { uniqueId } from 'lodash';

const ReactPlayer = loadable(
  () =>
    import(
      /* webpackChunkName: "VideoPlayer" */
      'react-player'
    )
);
const name = 'VideoPlayer';

const ItemVideoPlayer = styled('div', { name, slot: 'root' })(({ theme }) => ({
  width: '100%',
  height: '100%',
  display: 'flex',
  alignItems: 'center',
  overflow: 'hidden',
  position: 'relative',
  '& .react-player__preview': {
    position: 'relative',
    '&:before': {
      content: '""',
      position: 'absolute',
      top: 0,
      bottom: 0,
      left: 0,
      right: 0,
      backgroundColor: '#000',
      opacity: 0.4
    }
  },
  '& .ico-play-circle-o': {
    fontSize: theme.mixins.pxToRem(72),
    color: '#fff',
    position: 'relative'
  }
}));

const PlayerWrapper = styled('div', { name, slot: 'playerWrapper' })(
  ({ theme }) => ({
    width: '100%',
    '&:before': {
      content: '""',
      paddingTop: '56.25%',
      backgroundColor: theme.palette.grey['A700'],
      display: 'block',
      width: '100%'
    },
    '& .fb-video': {
      display: 'flex',
      alignItems: 'center'
    }
  })
);

const ThumbImageWrapper = styled('div', { name, slot: 'ThumbImageWrapper' })(
  ({ theme }) => ({
    width: '100%',
    height: '100%',
    position: 'absolute',
    top: 0,
    zIndex: 1,
    filter: 'brightness(0.7)'
  })
);

const ReactPlayerStyled = styled(ReactPlayer, { name, slot: 'reactPlayer' })(
  ({ theme }) => ({
    position: 'absolute',
    top: 0,
    left: 0,
    zIndex: 0,
    '.controls': {
      width: '10px'
    }
  })
);
const CustomPlayButton = styled(LineIcon, {
  name,
  slot: 'CustomPlayButton'
})({
  position: 'absolute !important',
  top: 0,
  bottom: 0,
  left: 0,
  right: 0,
  margin: 'auto',
  width: 'fit-content',
  height: 'fit-content',
  zIndex: 2,
  pointerEvents: 'none'
});
export interface VideoPlayerProps {
  src: string;
  thumb_url: any;
  modalUrl?: string;
  autoPlay?: boolean;
  autoplayIntersection?: boolean;
  idPlaying?: string;
}

const youtube_parser = url => {
  const regExp =
    /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
  const match = url.match(regExp);

  return match && match[7].length === 11 ? match[7] : false;
};

export default function VideoPlayer(props: VideoPlayerProps) {
  const {
    src,
    thumb_url,
    modalUrl,
    autoPlay = false,
    autoplayIntersection = false,
    idPlaying: idPlayingProp
  } = props;
  const { assetUrl, useMediaPlaying } = useGlobal();
  const classes = useStyles();
  const videoRef = React.useRef(null);
  const idPlaying = React.useMemo(
    () => idPlayingProp || uniqueId('video'),
    [idPlayingProp]
  );
  const [playing, setPlaying] = useMediaPlaying(idPlaying);
  const [refScrollInView, inView] = useInView({
    threshold: 0.5
  });

  const thumbUrl = getImageSrc(thumb_url, '500', assetUrl('video.no_image'));

  const handleReady = () => {
    if (autoPlay && !playing) {
      setPlaying(true);
    }
  };

  const playVideo = evt => {
    setPlaying(true);
  };

  const pauseVideo = evt => {
    setPlaying(false);
  };

  useEffect(() => {
    setPlaying(inView);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [inView]);

  if (!src) return null;

  const youtubeIdVideo = youtube_parser(src);
  // extra param support videoId in playlist, may be remove on future if react-player support auto get id video on playlist
  const configParamsExtra = youtubeIdVideo
    ? {
        youtube: {
          embedOptions: { videoId: youtubeIdVideo }
        }
      }
    : {};

  if (autoplayIntersection) {
    return (
      <ItemVideoPlayer ref={refScrollInView}>
        <PlayerWrapper>
          <ReactPlayerStyled
            ref={videoRef}
            url={src}
            width="100%"
            muted
            controls
            height="100%"
            onPause={pauseVideo}
            playing={playing}
            onPlay={playVideo}
            config={configParamsExtra}
            playIcon={<LineIcon icon="ico-play-circle-o" />}
          />
        </PlayerWrapper>
      </ItemVideoPlayer>
    );
  }

  if (modalUrl)
    return (
      <ItemVideoPlayer>
        <PlayerWrapper>
          <RouteLink role="link" to={modalUrl} asModal>
            <ThumbImageWrapper>
              <Image
                src={thumbUrl}
                aspectRatio={'fixed'}
                imgClass={classes.imageThumbnail}
              />
            </ThumbImageWrapper>
          </RouteLink>
          <CustomPlayButton icon="ico-play-circle-o" />
        </PlayerWrapper>
      </ItemVideoPlayer>
    );

  return (
    <ItemVideoPlayer ref={videoRef}>
      <PlayerWrapper>
        <ReactPlayerStyled
          url={src}
          controls
          width="100%"
          height="100%"
          onReady={handleReady}
          playing={playing}
          onPlay={playVideo}
          light={!autoPlay && thumbUrl}
          onPause={pauseVideo}
          muted
          config={configParamsExtra}
          playIcon={<LineIcon icon="ico-play-circle-o" />}
        />
      </PlayerWrapper>
    </ItemVideoPlayer>
  );
}
