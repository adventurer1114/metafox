import { Link, useGlobal } from '@metafox/framework';
import {
  FeaturedFlag,
  Image,
  SponsorFlag,
  Statistic,
  TruncateText
} from '@metafox/ui';
import VideoPlayer from '@metafox/ui/VideoPlayer';
import { getImageSrc } from '@metafox/utils';
import { VideoItemShape } from '@metafox/video';
import { Box, styled } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';

const name = 'VideoEmbedView';

const PlayerWrapper = styled('div', { name, slot: 'playerWrapper' })(
  ({ theme }) => ({
    marginLeft: theme.spacing(-2),
    marginRight: theme.spacing(-2)
  })
);

const WrapperInfoFlag = styled('div', { name, slot: 'wrapperInfoFlag' })(
  ({ theme }) => ({
    marginTop: 'auto',
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'flex-end'
  })
);
const FlagWrapper = styled('span', {
  name,
  slot: 'flagWrapper'
})(({ theme }) => ({
  marginLeft: 'auto',
  '& > .MuiFlag-root': {
    marginLeft: theme.spacing(2.5)
  }
}));

export default function VideoEmbedView({ item }: { item: VideoItemShape }) {
  const classes = useStyles();
  const { assetUrl } = useGlobal();
  const src = item.video_url || item.destination;

  return (
    <>
      <PlayerWrapper>
        {item?.is_processing ? (
          <div>
            <Image
              src={getImageSrc(
                null,
                '1024',
                assetUrl('video.video_in_processing_image')
              )}
              aspectRatio={'169'}
            />
          </div>
        ) : (
          <VideoPlayer
            src={src}
            thumb_url={item.image}
            autoplayIntersection
            // modalUrl={item.link}
          />
        )}
      </PlayerWrapper>
      <div className={classes.itemInner} data-testid="embedview">
        <Box mb={1} fontWeight={600} className={classes.title}>
          <Link to={item.link} asModal>
            <TruncateText variant="h4" lines={1}>
              {item.title}
            </TruncateText>
          </Link>
        </Box>
        <WrapperInfoFlag>
          <div>
            <Statistic
              values={item.statistic}
              display="total_view"
              fontStyle="minor"
              skipZero={false}
            />
          </div>
          <FlagWrapper>
            <FeaturedFlag
              variant="text"
              value={item?.is_featured}
              color="primary"
            />
            <SponsorFlag
              color="yellow"
              variant="text"
              value={item?.is_sponsor}
            />
          </FlagWrapper>
        </WrapperInfoFlag>
      </div>
    </>
  );
}
