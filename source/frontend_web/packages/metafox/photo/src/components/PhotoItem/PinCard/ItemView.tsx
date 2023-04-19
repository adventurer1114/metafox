import { Link, useGlobal } from '@metafox/framework';
import { PhotoItemProps } from '@metafox/photo/types';
import { FeaturedFlag, LineIcon, SponsorFlag } from '@metafox/ui';
import { getImageSize, getImageSrc } from '@metafox/utils';
import clsx from 'clsx';
import React from 'react';
import useStyles from './styles';

export interface PhotoProps {
  identity: string;
  itemHeight?: number;
  width: number;
  height: number;
  margin: number;
  classes?: any;
  order: number;
  pushItem?: (id: string, width: number, height: number, order: number) => void;
  onDeleteImage?: (id: string | number) => void;
}

const PhotoItem_PinCard = ({
  identity,
  item,
  user,
  height,
  width,
  itemHeight,
  margin,
  onDeleteImage,
  pushItem,
  handleAction,
  state,
  wrapAs: WrapAs,
  wrapProps,
  order
}: PhotoItemProps & PhotoProps) => {
  const { ItemActionMenu, assetUrl, getAcl } = useGlobal();
  const canViewReactionItem = getAcl('like.like.view');
  const imageHeight = itemHeight ? '100%' : 'auto';
  const classes = useStyles();
  const no_image =
    item?.resource_name === 'video'
      ? assetUrl('video.no_image')
      : assetUrl('photo.no_image');
  const src = getImageSrc(item?.image, '500', no_image);

  const resize = async () => {
    try {
      const size = await getImageSize(src);

      pushItem(identity, size.width, size.height, order);
    } catch (err) {
      // console.log(err);
    }
  };

  React.useEffect(() => {
    if (!width && pushItem && src) {
      resize();

      return;
    }

    if (pushItem) pushItem(identity, 0, 0, order);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [identity, pushItem, src, width]);

  if (!item || item?.error) return null;

  if (!width || !height) return null;

  const { id, is_featured, is_sponsor, title, statistic, resource_name } = item;
  let to = `/photo/${id}`;

  if (item?.album_id) {
    to =
      resource_name === 'photo'
        ? `/media/album/${item?.album_id}/photo/${id}`
        : `/media/album/${item?.album_id}/video/${id}`;
  }

  return (
    <WrapAs {...wrapProps} className={classes.pinGird}>
      <div
        className={clsx(classes.pinItem, state.menuOpened && classes.hoverCard)}
        style={{ height: itemHeight, margin }}
      >
        <div className={classes.features}>
          <FeaturedFlag variant="itemView" value={item.is_featured} />
          <SponsorFlag variant="itemView" value={item.is_sponsor} />
        </div>
        <Link to={to} asModal className={classes.mediaLink}>
          <img
            src={src}
            alt={title}
            className={classes.image}
            style={{ height: imageHeight }}
          />
          {resource_name === 'photo' ? (
            <div className={classes.photoInfo}>
              <div>
                <div className={classes.photoTitle}>{user.full_name}</div>
                {canViewReactionItem && statistic.total_like ? (
                  <div className={classes.photoLike}>
                    <span className="ico ico-thumbup-o"></span>
                    <span className={classes.total_like}>
                      {statistic.total_like}
                    </span>
                  </div>
                ) : null}
              </div>
            </div>
          ) : null}
          {resource_name === 'video' ? (
            <div className={classes.playVideoIcon}>
              <LineIcon icon="ico-play-circle-o" />
            </div>
          ) : null}

          {!item?.album_id ? (
            <div className={classes.features}>
              {is_featured ? (
                <FeaturedFlag variant="itemView" value={is_featured} />
              ) : null}
              {is_sponsor ? (
                <SponsorFlag variant="itemView" value={is_sponsor} />
              ) : null}
            </div>
          ) : null}
        </Link>
        <div className={classes.photoActions}>
          <ItemActionMenu
            identity={identity}
            state={state}
            handleAction={handleAction}
            className={classes.photoActionsDropdown}
          >
            <LineIcon
              icon={'ico-dottedmore-vertical-o'}
              className={classes.iconButton}
            />
          </ItemActionMenu>
        </div>
      </div>
    </WrapAs>
  );
};
export default PhotoItem_PinCard;
