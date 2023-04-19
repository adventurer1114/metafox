/**
 * @type: itemView
 * name: activitypoint.itemView.packageItem
 */

import { useGetItem, useGlobal } from '@metafox/framework';
import { Typography, Box, styled, Button, Card } from '@mui/material';
import React from 'react';
import { Image, ItemView } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';

const ItemViewStyled = styled(ItemView)(({ theme }) => ({
  maxWidth: '100%'
}));

const PackageItemStyled = styled(Card, { name: 'PackageItemStyled' })(
  ({ theme }) => ({
    padding: theme.spacing(4),
    width: '100%',
    height: '100%'
  })
);

const PointStyled = styled(Typography, { name: 'PointStyled' })(
  ({ theme }) => ({
    fontWeight: theme.typography.fontWeightRegular,
    color: theme.palette.primary.main,
    marginTop: theme.spacing(3)
  })
);

const Transactions = ({ identity, ...props }: any) => {
  const { i18n, dispatch, assetUrl } = useGlobal();

  const item = useGetItem(identity);

  const handlePurchase = () => {
    dispatch({ type: 'activityPoint/purchase', payload: { identity } });
  };

  return (
    <ItemViewStyled {...props}>
      <PackageItemStyled>
        <Box>
          <Typography variant="subtitle1" sx={{ mb: 2 }}>
            {item.title}
          </Typography>
          <Box sx={{ width: '100px', height: '100px' }}>
            <Image
              src={getImageSrc(
                item.image,
                '500',
                assetUrl('activitypoint.package_no_image')
              )}
              squareImg="square100px"
            />
          </Box>
          <PointStyled variant="h1">
            {i18n.formatMessage({ id: 'points' }, { amount: item.amount })}
          </PointStyled>
          <Typography variant="body1" color="text.secondary">
            {item.price_string}
          </Typography>
        </Box>
        <Button
          onClick={handlePurchase}
          sx={{ mt: 4, width: '100%' }}
          variant="outlined"
        >
          {i18n.formatMessage({ id: 'purchase' })}
        </Button>
      </PackageItemStyled>
    </ItemViewStyled>
  );
};

export default Transactions;
