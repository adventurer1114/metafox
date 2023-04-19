import { Link } from '@metafox/framework';
import {
  ItemMedia,
  ItemText,
  ItemView,
  Statistic,
  UserAvatar
} from '@metafox/ui';
import { UserItemProps } from '@metafox/user/types';
import { Typography } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';

export default function UserItem({
  item,
  identity,
  itemProps,
  wrapAs,
  wrapProps
}: UserItemProps) {
  const classes = useStyles();

  if (!item) return null;

  const { statistic, id, full_name, link: to } = item;

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <ItemMedia>
        <UserAvatar user={item as any} size={48} hoverCard={`/user/${id}`} />
      </ItemMedia>
      <ItemText>
        <Typography
          variant="subtitle1"
          className={classes.title}
          component="div"
        >
          <Link to={to} children={full_name} hoverCard={`/user/${id}`} />
        </Typography>
        <div role="button" className={classes.stats}>
          <Statistic values={statistic} display="total_mutual" />
        </div>
      </ItemText>
    </ItemView>
  );
}

UserItem.displayName = 'UserItemSmallCard';
