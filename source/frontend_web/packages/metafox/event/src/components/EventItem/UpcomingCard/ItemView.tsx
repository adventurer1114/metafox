import { EventItemProps } from '@metafox/event/types';
import { Link, useGlobal } from '@metafox/framework';
import { FormatDate, ItemView, TruncateText } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import clsx from 'clsx';
import React from 'react';
import LoadingSkeleton from './LoadingSkeleton';
import useStyles from './styles';

export default function EventUpcomingCardItem({
  item,
  identity,
  wrapProps,
  wrapAs
}: EventItemProps) {
  const classes = useStyles();
  const { i18n, assetUrl } = useGlobal();

  if (!item) return null;

  const to = `/event/${item.id}`;

  const cover = getImageSrc(item.image, '500', assetUrl('event.no_image'));

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <div className={clsx(classes.root, classes.upcomingItem)}>
        <div className={classes.outer}>
          {cover ? (
            <Link to={to}>
              <span
                className={classes.bgImage}
                style={{ backgroundImage: `url(${cover})` }}
              ></span>
            </Link>
          ) : null}
          <div className={classes.inner}>
            <Link to={to} className={classes.title}>
              <TruncateText variant={'subtitle2'} lines={1} fixHeight>
                {item.title}
              </TruncateText>
            </Link>
            <div className={clsx(classes.itemMinor, classes.startDate)}>
              <FormatDate
                data-testid="startTime"
                value={item.start_time}
                format="MMMM DD, yyyy"
              />{' '}
              {item.location[0] && i18n.formatMessage({ id: 'at' })}{' '}
              {item.location[0]}
            </div>
          </div>
        </div>
      </div>
    </ItemView>
  );
}

EventUpcomingCardItem.LoadingSkeleton = LoadingSkeleton;
