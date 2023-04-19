import { useGlobal } from '@metafox/framework';
import { Grid } from '@mui/material';
import * as React from 'react';

const CustomItem = props => {
  const { item, pushItem, order } = props;
  const { jsxBackend } = useGlobal();
  const ref = React.useRef();

  const Item = jsxBackend.get(item.component);

  React.useEffect(() => {
    if (ref?.current && pushItem) {
      pushItem(item, 125, 125, order);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  if (!Item) return null;

  return (
    <Grid
      ref={ref}
      item
      key="itemAddPhotoAlbum"
      style={{ width: '100%', paddingTop: 0 }}
    >
      <Item size="large" />
    </Grid>
  );
};

export default CustomItem;
