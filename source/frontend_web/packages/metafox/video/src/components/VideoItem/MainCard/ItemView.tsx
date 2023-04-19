import { Link, useGlobal } from '@metafox/framework';
import { useBlock } from '@metafox/layout';
import {
  FeaturedFlag,
  Image,
  ItemMedia,
  ItemSummary,
  ItemText,
  ItemTitle,
  ItemView,
  LineIcon,
  PendingFlag,
  SponsorFlag,
  Statistic
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { VideoItemProps } from '@metafox/video/types';
import { Box, styled } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';

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

const VideoItemMainCard = ({
  item,
  user,
  identity,
  itemProps,
  handleAction,
  state,
  wrapAs,
  wrapProps
}: VideoItemProps) => {
  const classes = useStyles();
  const { ItemActionMenu, assetUrl, useIsMobile } = useGlobal();
  const { itemLinkProps, itemProps: { media = {} } = {} } = useBlock();
  const isMobile = useIsMobile();

  if (!item) return null;

  const to = `/video/play/${item.id}`;

  const noImage = item?.is_processing
    ? assetUrl('video.video_in_processing_image')
    : assetUrl('video.no_image');
  const cover = getImageSrc(item.image, '500', noImage);

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <div className={classes.itemFlag}>
        <FeaturedFlag variant="itemView" value={item.is_featured} />
        <SponsorFlag variant="itemView" value={item.is_sponsor} />
        <PendingFlag variant="itemView" value={item.is_pending} />
      </div>
      <ItemMedia src={cover} backgroundImage>
        <Link to={to} asModal sx={{ position: 'relative' }} {...itemLinkProps}>
          <Image src={cover} {...media} />
          <OverlayStyled isMobile={isMobile}>
            <LineIcon className="iconPlay" icon="ico-play-circle-o" />
          </OverlayStyled>
        </Link>
      </ItemMedia>
      <ItemText>
        <ItemTitle className={classes.itemTitle}>
          <Link to={to} asModal {...itemLinkProps}>
            {item.title}
          </Link>
        </ItemTitle>
        <ItemSummary>
          <Link
            to={`/${user?.user_name}`}
            children={user.full_name}
            data-testid="itemAuthor"
          />
        </ItemSummary>
        {item.statistic?.total_view ? (
          <ItemSummary>
            <Statistic
              color="text.hint"
              values={item.statistic}
              display={'total_view'}
            />
          </ItemSummary>
        ) : null}
        {itemProps.showActionMenu ? (
          <ItemActionMenu
            identity={identity}
            icon={'ico-dottedmore-vertical-o'}
            handleAction={handleAction}
            className={classes.actionMenu}
          />
        ) : null}
      </ItemText>
    </ItemView>
  );
};

export default VideoItemMainCard;
