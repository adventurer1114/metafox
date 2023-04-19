/**
 * @type: itemView
 * name: activitypoint.itemView.transaction
 */

import { useGetItem } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Grid, styled, Theme } from '@mui/material';
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

const PointStyled = styled(Grid, {
  name: 'PointStyled',
  shouldForwardProp: prop => prop !== 'lostColor'
})(({ theme, lostColor }: { theme: Theme; lostColor: boolean }) => ({
  color: lostColor ? theme.palette.error.main : theme.palette.success.main
}));

enum LostColor {
  SENT = 3,
  SPENT = 4,
  RETRIEVED = 6
}

const colorMapping = [LostColor.SENT, LostColor.SPENT, LostColor.RETRIEVED];

const Transactions = ({ identity }: any) => {
  const item = useGetItem(identity);

  if (!item) return null;

  const {
    type_name,
    package_name,
    action,
    id,
    creation_date,
    type_id,
    points
  } = item;

  return (
    <ItemWrapperStyled container>
      <Grid item xs={2}>
        {package_name}
      </Grid>
      <PointStyled item xs={1} lostColor={colorMapping.includes(type_id)}>
        {points}
      </PointStyled>
      <Grid item xs={5}>
        <HtmlViewer html={action} />
      </Grid>
      <Grid item xs={1}>
        {type_name}
      </Grid>
      <Grid item xs={1}>
        {id}
      </Grid>
      <Grid item xs={2}>
        {new Date(creation_date).toLocaleDateString()}
      </Grid>
    </ItemWrapperStyled>
  );
};

export default Transactions;
