/**
 * @type: ui
 * name: menuItem.as.sidebarHeading
 */
import { ControlMenuItemProps } from '@metafox/ui';
import React from 'react';
import { ListSubheader, styled } from '@mui/material';

const List = styled(ListSubheader, {
  name: 'List'
})(({ theme }) => ({
  color: theme.palette.text.primary,
  fontSize: '24px',
  [theme.breakpoints.down('md')]: {
    display: 'none'
  }
}));

export default function SideBarHeadingMenuItem(props: ControlMenuItemProps) {
  const { item, classes } = props;

  return (
    <List className={classes.menuHeading} data-testid={item.testid}>
      {item.label}
    </List>
  );
}
