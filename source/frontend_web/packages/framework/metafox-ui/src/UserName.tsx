import { Link, LinkProps } from '@metafox/framework';
import * as React from 'react';
import { ItemUserShape } from '.';

type UserNameProps = {
  user: ItemUserShape;
  to?: string;
  className?: string;
} & LinkProps;

const UserName = ({
  user,
  to,
  color = 'primary',
  underline = 'hover',
  ...rest
}: UserNameProps) => {
  return (
    <Link
      underline={underline}
      color={color}
      hoverCard={`/user/${user.id}`}
      to={to ? to : `/${user.user_name}`}
      {...rest}
    >
      {user.full_name}
    </Link>
  );
};

export default UserName;
