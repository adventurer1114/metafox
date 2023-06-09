import { Link, useGlobal } from '@metafox/framework';
import { LivestreamItemProps } from '@metafox/livestreaming/types';
import {
  Flag,
  Image,
  ItemView,
  LineIcon,
  Statistic,
  TruncateText
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import * as React from 'react';
import useStyles from './styles';

const LivestreamItemSmallCard = ({
  item,
  user,
  identity,
  itemProps,
  handleAction,
  state,
  wrapAs,
  wrapProps
}: LivestreamItemProps) => {
  const { ItemActionMenu, assetUrl } = useGlobal();
  const classes = useStyles();

  if (!item) return null;

  const to = `/livestream/play/${item.id}/${item.title}`;
  const cover = getImageSrc(item.image, '500', assetUrl('livestream.no_image'));

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <div className={classes.root}>
        <div className={classes.outer}>
          <div className={classes.itemFlag}>
            {item.is_featured ? (
              <Flag
                data-testid="featured"
                type={'is_featured'}
                color={'white'}
                hasShadow
                variant={'itemView'}
              />
            ) : null}
            {item.is_sponsor ? (
              <Flag
                data-testid="sponsored"
                type={'is_sponsor'}
                color={'white'}
                hasShadow
                variant={'itemView'}
              />
            ) : null}
          </div>
          {cover ? (
            <Link to={to} className={classes.media}>
              <LineIcon className={classes.iconPlay} icon="ico-play-circle-o" />
              <Image
                link={to}
                src={cover}
                aspectRatio={'169'}
                backgroundImage
              />
            </Link>
          ) : null}
          <div className={classes.inner}>
            <Link to={to} className={classes.title}>
              <TruncateText variant={'body1'} lines={2} fixHeight>
                {item.title}
              </TruncateText>
            </Link>
            <div className={classes.itemMinor}>
              <Link to={`/user/${user?.id}`} children={user.full_name} />
            </div>
            <Statistic
              className={classes.itemMinor}
              values={item.statistic}
              display={'total_view'}
            />
            {!itemProps.showActionMenu ? (
              <ItemActionMenu
                identity={identity}
                icon={'ico-dottedmore-vertical-o'}
                state={state}
                handleAction={handleAction}
                className={classes.actionMenu}
              />
            ) : null}
          </div>
        </div>
      </div>
    </ItemView>
  );
};

export default LivestreamItemSmallCard;
