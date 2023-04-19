import { RefOf, useGlobal, useLocation } from '@metafox/framework';
import { AutoCompactMenu, MenuItemShape } from '@metafox/ui';
import { filterShowWhen } from '@metafox/utils';
import { Box, Button, ListItem, ListItemText } from '@mui/material';
import React from 'react';

// 1 day in milliseconds
const MS_1_HOUR = 36e5;

type ItemProps = {
  item: MenuItemShape;
  activeTab?: string;
};

const SecondMenuItem = ({ item, activeTab }: ItemProps) => {
  const { to, label } = item;
  const { navigate } = useGlobal();

  const isActive = activeTab === to;

  const handleClick = () => {
    navigate(to);
  };

  return (
    <ListItem
      onClick={handleClick}
      data-testid={item.testid ?? item.name}
      sx={{
        cursor: 'pointer',
        backgroundColor: isActive ? 'background.default' : 'unset'
      }}
    >
      <ListItemText>{label}</ListItemText>
    </ListItem>
  );
};

const MenuItem = ({ item, activeTab }: ItemProps) => {
  const { to, label, value, params } = item;

  const { navigate, dispatch } = useGlobal();

  const handleClick = () => {
    if (!item.value) {
      navigate(to);
    } else {
      dispatch({ type: value, payload: params });
    }
  };

  const active = activeTab === to;

  return (
    <Box sx={{ px: 1 }}>
      <Button
        data-testid={item.testid ?? item.name}
        size="small"
        variant="text"
        sx={{
          // textDecoration: active ? 'underline' : 'none',
          fontWeight: active ? 'bold' : 'normal'
        }}
        onClick={handleClick}
      >
        {label}
      </Button>
    </Box>
  );
};

interface MoreButtonProps {
  onClick: () => void;
}

const MoreButton = React.forwardRef(
  (props: MoreButtonProps, ref: RefOf<HTMLButtonElement>) => {
    const { i18n } = useGlobal();

    return (
      <Button
        size="small"
        variant="outlined"
        color="primary"
        ref={ref}
        // endIcon={<LineIcon icon="ico-caret-down" />}
        sx={{ fontWeight: 'normal' }}
        {...props}
      >
        {i18n.formatMessage({ id: 'more' })}
      </Button>
    );
  }
);

const Menu = ({ menuName }: { appName: string; menuName: string }) => {
  const { useFetchDetail, getSetting, getAcl } = useGlobal();
  const location = useLocation();
  const [data] = useFetchDetail({
    dataSource: {
      apiUrl: `/admincp/menu/${menuName}`
    },
    forceReload: false,
    cachePrefix: 'menu',
    cacheKey: menuName,
    ttl: MS_1_HOUR
  });

  if (!data || !data.length) return null;

  const items = filterShowWhen(data, {
    settings: getSetting(),
    acl: getAcl()
  });

  if (!items.length) return null;

  // auto collapse menu
  return (
    <Box
      style={{ display: 'flex', flex: 1, overflow: 'hidden' }}
      data-testid="secondaryMenu"
    >
      <AutoCompactMenu
        items={items}
        activeTab={location.pathname}
        SecondMenuItem={SecondMenuItem}
        MoreButton={MoreButton}
        MenuItem={MenuItem}
        menuName={menuName}
      />
    </Box>
  );
};

export default Menu;
