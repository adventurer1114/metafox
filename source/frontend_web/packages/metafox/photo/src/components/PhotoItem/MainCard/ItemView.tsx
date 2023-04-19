import { Link, useGlobal } from '@metafox/framework';
import { PhotoItemProps } from '@metafox/photo/types';
import {
  FeaturedFlag,
  ItemMedia,
  ItemView,
  PendingFlag,
  SponsorFlag
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import React from 'react';
import useStyles from './styles';

const PhotoItemMainCard = ({
  item,
  user,
  identity,
  handleAction,
  state,
  wrapAs,
  wrapProps
}: PhotoItemProps) => {
  const classes = useStyles();
  const { ItemActionMenu, assetUrl, getAcl } = useGlobal();

  if (!item) return null;

  const canViewReactionItem = getAcl('like.like.view');
  const { id, statistic } = item;
  const to = `/photo/${id}`;
  const cover = getImageSrc(item.image, '500', assetUrl('photo.no_image'));

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
      className={classes.root}
    >
      <ItemMedia src={cover} backgroundImage />
      <Link to={to} asModal>
        <div className={classes.photoInfo}>
          <div className={classes.photoTitle}>{user.full_name}</div>

          {canViewReactionItem && statistic.total_like ? (
            <div className={classes.photoLike}>
              <span className="ico ico-thumbup-o"></span>
              <span className={classes.total_like}>{statistic.total_like}</span>
            </div>
          ) : null}
        </div>
      </Link>
      <div className={classes.features}>
        <FeaturedFlag variant="itemView" value={item.is_featured} />
        <SponsorFlag variant="itemView" value={item.is_sponsor} />
        <PendingFlag variant="itemView" value={item.is_pending} />
      </div>
      <div className={classes.photoActions}>
        <ItemActionMenu
          state={state}
          identity={identity}
          handleAction={handleAction}
          className={classes.photoActionsDropdown}
          icon={'ico-dottedmore-vertical-o'}
        />
      </div>
    </ItemView>
  );
};

export default PhotoItemMainCard;
