import { useGlobal } from '@metafox/framework';
import { MenuItemShape } from '@metafox/ui';
import { filterShowWhen } from '@metafox/utils';
import {
  ClickAwayListener,
  IconButton,
  IconButtonProps,
  MenuItem,
  Paper,
  Popper,
  PopperProps
} from '@mui/material';
import React from 'react';
import LineIcon from '../LineIcon';
import useStyles from './styles';

export interface DropdownMenuProps {
  items: MenuItemShape[];
  prefixName?: string;
  fallbackName?: string;
  control?: React.ReactElement;
  data?: object;
  label?: string;
  icon?: string;
  id?: string;
  size?: IconButtonProps['size'];
  placement?: PopperProps['placement'];
  handleAction: (type: string, payload?: object, meta?: object) => void;
  iconClass?: string;
  buttonClass?: string;
  item?: Record<string, any>;
}

export default function DropdownMenu({
  item,
  label = 'Options',
  items: itemsProps,
  icon = 'ico-gear-o',
  size = 'small',
  id = 'settings',
  data,
  handleAction,
  buttonClass,
  iconClass,
  placement = 'bottom-end'
}: DropdownMenuProps) {
  const anchorRef = React.useRef<HTMLDivElement>();
  const [open, setOpen] = React.useState<boolean>(false);
  const { getSetting } = useGlobal();
  const paperRef = React.useRef();
  const classes = useStyles();

  const handleClick = (evt: unknown, item: MenuItemShape) => {
    if (evt) (evt as React.MouseEvent).stopPropagation();

    setOpen(open => !open);

    handleAction(item.value, { ...item.params, ...data });
  };

  const items = filterShowWhen(itemsProps, { item, setting: getSetting() });

  const handleOnClick = (evt: React.MouseEvent<HTMLButtonElement>) => {
    if (evt) evt.stopPropagation();

    setOpen(!open);
  };

  const handleOutsideClick = React.useCallback(() => {
    if (open) setOpen(false);
  }, [open]);

  if (!items.length) return null;

  return (
    <>
      <IconButton
        onClick={handleOnClick}
        ref={anchorRef}
        aria-label={label}
        aria-controls={id}
        size={size}
        tabIndex={-1}
        className={buttonClass}
        aria-haspopup="menu"
        aria-expanded={!!open}
      >
        <LineIcon icon={icon} className={iconClass} />
      </IconButton>
      {open ? (
        <ClickAwayListener
          mouseEvent="onMouseDown"
          touchEvent="onTouchStart"
          onClickAway={handleOutsideClick}
        >
          <Popper
            open={Boolean(open)}
            anchorEl={anchorRef.current}
            className={classes.popper}
            placement={placement}
          >
            <Paper
              style={{
                maxHeight: '40vh'
              }}
              className={classes.menu}
              ref={paperRef}
            >
              {items.map(item => (
                <MenuItem
                  key={item.name}
                  onClick={evt => handleClick(evt, item)}
                >
                  {item.label}
                </MenuItem>
              ))}
            </Paper>
          </Popper>
        </ClickAwayListener>
      ) : null}
    </>
  );
}
