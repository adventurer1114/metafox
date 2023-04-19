import { ButtonLink, RouteLink } from '@metafox/framework';
import { LineIcon, MenuItemProps } from '@metafox/ui';
import { Avatar, Box, Divider, styled } from '@mui/material';
import clsx from 'clsx';
import React from 'react';

const MenuItemIcon = styled(LineIcon, {
  name: 'AboutIcon'
})(({ theme }) => ({
  marginRight: theme.spacing(2)
}));

function AsDivider({ dividerProps }: MenuItemProps) {
  return <Divider {...dividerProps} />;
}

function AsHeading({ label, classes }: MenuItemProps) {
  return <div className={classes.menuHeading}>{label}</div>;
}

function AsButton({
  classes,
  buttonProps,
  icon,
  to,
  label,
  testid
}: MenuItemProps) {
  return (
    <div className={classes.menuItemButton}>
      <ButtonLink
        {...buttonProps}
        to={to}
        role="button"
        data-testid={testid || label || icon}
      >
        {icon ? (
          <Box component="span" mr={0.5}>
            <LineIcon icon={icon} />
          </Box>
        ) : null}
        {label}
      </ButtonLink>
    </div>
  );
}

function AsLink({
  label,
  to,
  icon,
  image,
  alt,
  note,
  item_name,
  onClick,
  classes,
  active,
  testid
}: MenuItemProps) {
  return (
    <div
      className={clsx(classes.menuItem, active && classes.activeMenuItem)}
      role="menuitem"
      data-testid={testid || label || icon}
    >
      <RouteLink to={to} className={classes.menuItemLink} onClick={onClick}>
        {image || alt ? (
          <Avatar src={image} alt={alt} className={classes.menuItemAvatar} />
        ) : icon ? (
          <MenuItemIcon icon={icon} className={classes.menuItemIcon} />
        ) : null}
        <div className={classes.menuItemText}>
          <span>{label}</span>
          {note ? (
            <span className={classes.shortcutModuleName}>{note}</span>
          ) : null}
          {item_name ? (
            <div className={classes.itemName}>{item_name}</div>
          ) : null}
        </div>
      </RouteLink>
    </div>
  );
}

export default function SidebarMenuItem(props: MenuItemProps) {
  if ('divider' === props.as) {
    return <AsDivider {...props} />;
  }

  if ('heading' === props.as) {
    return <AsHeading {...props} />;
  }

  if ('button' === props.as) {
    return <AsButton {...props} />;
  }

  return <AsLink {...props} />;
}
