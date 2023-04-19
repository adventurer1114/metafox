/**
 * @type: itemView
 * name: activitypoint.itemView.package
 */

import { useGetItem } from '@metafox/framework';
import { Grid, styled } from '@mui/material';
import * as React from 'react';

const ItemWrapperStyled = styled(Grid, { name: 'ItemStyled' })(({ theme }) => ({
  fontSize: 15,
  color: theme.palette.text.secondary,
  marginTop: theme.spacing(2),
  backgroundColor: theme.palette.background.default,
  borderRadius: 8,
  display: 'flex',
  width: '100%',
  padding: theme.spacing(2)
}));

const Transactions = ({ identity }: any) => {
  const item = useGetItem(identity);

  if (!item) return null;

  const {
    status,
    package_name,
    package_price_string,
    date,
    package_point,
    id
  } = item;

  return (
    <ItemWrapperStyled container>
      <Grid item xs={3}>
        {package_name}
      </Grid>
      <Grid item xs={2}>
        {package_point}
      </Grid>
      <Grid item xs={2}>
        {package_price_string}
      </Grid>
      <Grid item xs={2}>
        {status}
      </Grid>
      <Grid item xs={1}>
        {id}
      </Grid>
      <Grid item xs={2}>
        {new Date(date).toLocaleDateString()}
      </Grid>
    </ItemWrapperStyled>
  );
};

export default Transactions;
