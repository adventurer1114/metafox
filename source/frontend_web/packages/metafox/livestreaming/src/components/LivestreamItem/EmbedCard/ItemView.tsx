import { Link, useGlobal } from '@metafox/framework';
import {
  FeaturedFlag,
  SponsorFlag,
  PendingFlag,
  Statistic,
  TruncateText,
  ItemMedia
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { LivestreamItemShape } from '@metafox/livestreaming';
import { Box, styled } from '@mui/material';
import * as React from 'react';

const name = 'LivestreamEmbedView';

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

const ItemInner = styled(Box, {
  name,
  slot: 'ItemInner'
})(({ theme }) => ({
  borderBottomLeftRadius: '8px',
  borderBottomRightRadius: '8px',
  border: theme.mixins.border('secondary'),
  borderTop: 'none',
  padding: theme.spacing(3),
  display: 'flex',
  flexDirection: 'column'
}));
const ItemFlag = styled(Box)(({ theme }) => ({
  position: 'absolute',
  right: 0,
  top: theme.spacing(2),
  display: 'flex',
  flexDirection: 'column',
  alignItems: 'flex-end',
  zIndex: 5
}));

export default function LivestreamEmbedView({
  item
}: {
  item: LivestreamItemShape;
}) {
  const { jsxBackend } = useGlobal();
  const { title, statistic, is_streaming } = item || {};
  const cover = getImageSrc(item.thumbnail_url, '500', '');
  const MediaLayer = jsxBackend.get('livestreaming.ui.overlayVideo');

  return (
    <Box>
      <PlayerWrapper>
        <ItemFlag>
          <FeaturedFlag variant="itemView" value={item.is_featured} />
          <SponsorFlag variant="itemView" value={item.is_sponsor} />
          <PendingFlag variant="itemView" value={item.is_pending} />
        </ItemFlag>
        <ItemMedia src={cover} backgroundImage>
          <MediaLayer item={item} />
        </ItemMedia>
      </PlayerWrapper>
      <ItemInner data-testid="embedview">
        {title ? (
          <Box mb={1} fontWeight={600}>
            <Link to={item.link} asModal>
              <TruncateText variant="h4" lines={1}>
                {title}
              </TruncateText>
            </Link>
          </Box>
        ) : null}
        {!is_streaming ? (
          <WrapperInfoFlag>
            <Statistic
              values={statistic}
              display="total_view"
              fontStyle="minor"
              skipZero={false}
            />
          </WrapperInfoFlag>
        ) : null}
      </ItemInner>
    </Box>
  );
}
