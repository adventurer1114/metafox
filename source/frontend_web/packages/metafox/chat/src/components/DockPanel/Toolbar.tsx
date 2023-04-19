import { MenuItems, MenuItemShape } from '@metafox/ui';
import { styled } from '@mui/material';
import { findIndex } from 'lodash';
import React from 'react';
import useStyles from './Toolbar.styles';

const name = 'ToolbarChat';

const Root = styled('div', {
  name,
  slot: 'root',
  shouldForwardProp: props => props !== 'variant'
})<{ variant?: string }>(({ theme, variant }) => ({
  display: 'flex',
  padding: '0',
  margin: '0',
  listStyle: 'none',
  '& .MuiActionMenu-menu': variant === 'roomPanel' && {
    overflow: 'auto',
    maxHeight: '200px',
    '&::-webkit-scrollbar': {
      height: '5px',
      width: '5px',
      background: 'transparent',
      borderRadius: theme.spacing(0.5),
      transition: 'opacity 200ms'
    },

    /* Track */
    '&::-webkit-scrollbar-track': {
      margin: theme.spacing(0.5, 0),
      borderRadius: theme.spacing(0.5),
      background:
        theme.palette.mode === 'light' ? 'white' : theme.palette.grey['300']
    },

    /* Handle */
    '&::-webkit-scrollbar-thumb': {
      background: theme.palette.grey['600'],
      borderRadius: theme.spacing(0.5)
    },

    /* Handle on hover */
    '&::-webkit-scrollbar-thumb:hover': {
      background: theme.palette.grey['700']
    },

    '&::-webkit-scrollbar-thumb:horizontal': {
      background: '#000',
      borderRadius: '10px'
    }
  }
}));

interface Props {
  items: MenuItemShape[];
  handleAction: (value: string) => void;
  displayLimit?: number;
  variant?: 'roomPanel' | 'pageMessage' | string;
}

export default function Toolbar({
  items,
  displayLimit = 2,
  variant = null,
  handleAction
}: Props) {
  const classes = useStyles();
  // TODO apply filter show when

  if (!Array.isArray(items)) return null;

  // dock chat
  if (variant === 'roomPanel') {
    const itemDisplay = displayLimit - 1;

    const toolbarItems = items.filter((item, index) => {
      return index < itemDisplay || item.behavior;
    });

    const found = findIndex(toolbarItems, { behavior: 'more' });

    if (found > -1) {
      if (!toolbarItems[found].items?.length) {
        const subitems = items.filter(
          (item, index) => index > itemDisplay - 1 && !item.behavior
        );

        toolbarItems[found] = {
          ...toolbarItems[found],
          items: subitems,
          as: 'dropdown'
        };
      } else {
        toolbarItems[found].as = 'dropdown';
      }
    }

    return (
      <Root variant={variant}>
        <MenuItems
          items={toolbarItems}
          prefixName="chatplusToolbar.item."
          fallbackName="button"
          classes={classes}
          handleAction={handleAction}
        />
      </Root>
    );
  }

  // page chat
  if (variant === 'pageMessage') {
    const toolbarItems = items.filter((item, index) => {
      return item.behavior;
    });

    const found = findIndex(toolbarItems, { behavior: 'more' });

    if (found > -1) {
      if (!toolbarItems[found].items?.length) {
        const subitems = items.filter((item, index) => !item.behavior);

        toolbarItems[found] = {
          ...toolbarItems[found],
          items: subitems,
          as: 'dropdown'
        };
      } else {
        toolbarItems[found].as = 'dropdown';
      }
    }

    return (
      <Root variant={variant}>
        <MenuItems
          items={toolbarItems}
          prefixName="chatplusToolbar.item."
          fallbackName="button"
          classes={classes}
          handleAction={handleAction}
          variant={variant}
        />
      </Root>
    );
  }

  // default
  const toolbarItems = items.filter(
    (item, index) => index < displayLimit - 1 || item.behavior
  );

  const found = findIndex(toolbarItems, { behavior: 'more' });

  if (found > -1) {
    if (!toolbarItems[found].items?.length) {
      const subitems = items.filter(
        (item, index) => index > displayLimit - 2 && !item.behavior
      );

      toolbarItems[found] = {
        ...toolbarItems[found],
        items: subitems,
        as: 'dropdown'
      };
    } else {
      toolbarItems[found].as = 'dropdown';
    }
  }

  return (
    <Root>
      <MenuItems
        items={toolbarItems}
        prefixName="chatplusToolbar.item."
        fallbackName="button"
        classes={classes}
        handleAction={handleAction}
      />
    </Root>
  );
}
