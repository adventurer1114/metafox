import { useGlobal } from '@metafox/framework';
import { Button } from '@mui/material';
import { isEmpty } from 'lodash';
import React from 'react';

function ListButtonAction({ identity, menu, item: itemAdvertise }: any) {
  const { i18n, dispatch } = useGlobal();

  if (isEmpty(menu)) return null;

  const handleAction = (type: string) => {
    dispatch({
      type,
      payload: {
        identity
      }
    });
  };

  return (
    <>
      {menu.map(item => (
        <Button
          key={item.name}
          onClick={() => handleAction(item.value)}
          data-testid={`button${item.name}`}
          color={item?.color || 'primary'}
          variant={item?.variant || 'outlined'}
          sx={{ mr: 1 }}
        >
          {i18n.formatMessage(
            { id: item?.label },
            { price: itemAdvertise?.payment_price }
          )}
        </Button>
      ))}
    </>
  );
}

export default ListButtonAction;
