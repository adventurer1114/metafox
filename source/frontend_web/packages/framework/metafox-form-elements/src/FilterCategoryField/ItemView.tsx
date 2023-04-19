import { LineIcon } from '@metafox/ui';
import { IconButton } from '@mui/material';
import clsx from 'clsx';
import { isEmpty } from 'lodash';
import * as React from 'react';

export interface CategoryItemShape {
  id: number;
  name: string;
  resource_name: string;
  subs: CategoryItemShape[];
}

export interface CategoryItemViewProps {
  item: CategoryItemShape;
  handleSelect: (id: string, name: string) => void;
  active?: boolean;
  [key: string]: any;
}

type TCategoryItemClassKey = Record<
  'subCategory' | 'link' | 'item' | 'itemActive' | 'span' | 'icon' | 'hasSubs',
  string
>;

type TCategoryItemViewStyles = { classes?: TCategoryItemClassKey };

const checkSubNestedActive = (data, idActive) => {
  if (data?.subs?.length > 0) {
    if (
      data.subs.some(
        x => x.id.toString() === idActive || checkSubNestedActive(x, idActive)
      )
    )
      return true;
  }

  return false;
};

export default function ItemView({
  item,
  handleSelect,
  active,
  classes,
  category_id
}: CategoryItemViewProps & TCategoryItemViewStyles) {
  const [open, setOpen] = React.useState<boolean>(false);
  const idActive = category_id;

  React.useEffect(() => {
    if (checkSubNestedActive(item, idActive)) {
      setOpen(true);

      return;
    }

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [idActive]);

  const toggleSub = React.useCallback(() => {
    setOpen(prev => !prev);
  }, []);

  const handleChangeSelectItem = (id, resource_name) => {
    handleSelect(id, resource_name);
  };

  return (
    <div className={classes.item}>
      <div
        className={clsx(
          !isEmpty(item.subs) && classes.hasSubs,
          active && classes.itemActive
        )}
      >
        <div
          onClick={() => handleChangeSelectItem(item.id, item.resource_name)}
          data-testid="itemCategory"
          className={classes.link}
          color={'inherit'}
          aria-selected={active}
          aria-label={item.name}
        >
          <span className={classes.span}>{item.name}</span>
        </div>
        {isEmpty(item.subs) ? null : (
          <IconButton size="small" className={classes.icon} onClick={toggleSub}>
            <LineIcon icon={open ? 'ico-angle-up' : 'ico-angle-down'} />
          </IconButton>
        )}
      </div>
      {open && item?.subs?.length > 0 ? (
        <ul className={classes.subCategory}>
          {item?.subs.map((subItem, index) => {
            return (
              <div key={subItem.id}>
                <ItemView
                  item={subItem}
                  classes={classes}
                  handleSelect={handleChangeSelectItem}
                  active={subItem.id.toString() === idActive}
                  category_id={category_id}
                />
              </div>
            );
          })}
        </ul>
      ) : null}
    </div>
  );
}
