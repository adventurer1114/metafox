/**
 * @type: itemView
 * name: advertise.itemView.sponsorshipRecord
 */

import { useGetItem, Link, useGlobal } from '@metafox/framework';
import { FormatNumber } from '@metafox/ui';
import { Grid, styled, Switch } from '@mui/material';
import moment from 'moment';
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

const AdHome = ({ identity }: any) => {
  const { dispatch } = useGlobal();
  const item = useGetItem(identity);

  if (!item) return null;

  const { title, link, start_date, status, type, is_active, statistics } = item;

  const handleChangeActive = () => {
    dispatch({
      type: 'advertise/activeItem',
      payload: {
        identity,
        active: !is_active
      }
    });
  };

  return (
    <ItemWrapperStyled container>
      <Grid item xs={3}>
        <Link to={link} underline="none">
          {title}
        </Link>
      </Grid>

      <Grid item xs={2}>
        {start_date ? moment(start_date).format('LL') : ''}
      </Grid>
      <Grid item xs={1}>
        {status}
      </Grid>
      <Grid item xs={2}>
        {type}
      </Grid>
      <Grid item xs={1}>
        <FormatNumber value={statistics?.total_impression} />
      </Grid>
      <Grid item xs={1}>
        <FormatNumber value={statistics?.total_click} />
      </Grid>
      <Grid item xs={2}>
        <Switch
          checked={is_active}
          onChange={handleChangeActive}
          inputProps={{ 'aria-label': 'controlled' }}
        />
      </Grid>
    </ItemWrapperStyled>
  );
};

export default AdHome;
