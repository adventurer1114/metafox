import { RouteLink, useGlobal } from '@metafox/framework';
import React from 'react';
import { StoreProductItemShape as ItemShape } from '@metafox/core/types';
import { ItemViewProps, LineIcon } from '@metafox/ui';
import { styled } from '@mui/material/styles';
import { Typography, Box } from '@mui/material';

const Image = styled('div', {
  name: 'StoreProductItem',
  slot: 'Image'
})<{ src: string }>(({ theme, src }) => ({
  backgroundImage: `url(${src})`,
  width: '100%',
  height: 250,
  display: 'block',
  borderRadius: '8px',
  backgroundPosition: 'center',
  backgroundSize: 'cover',
  backgroundRepeat: 'no-repeat',
  marginBottom: '16px',
  border: '1px solid',
  borderColor: theme.palette.border.secondary
}));

const Title = styled(RouteLink, { name: 'Title' })(({ theme }) => ({
  fontSize: 24,
  lineHeight: '28px'
}));

const Author = styled(Typography, { name: 'Author' })(({ theme }) => ({
  color: theme.palette.grey[700],
  fontSize: 16,
  lineHeight: '28px'
}));

const Price = styled(Typography, { name: 'Price' })(({ theme }) => ({
  color: theme.palette.warning.main,
  fontSize: 18,
  lineHeight: '28px'
}));

export default function ProductItem(props: ItemViewProps<ItemShape>) {
  const { i18n } = useGlobal();
  const { item } = props;

  if (!item) return null;

  const { discount, price } = item;

  return (
    <Box sx={{ width: '33.3%', padding: '15px 20px', color: '#555' }}>
      <RouteLink to={`/admincp/app/store/product/${item.id}`}>
        <Image src={item.image[500]} />
        <Title to={`/admincp/app/store/product/${item.id}`}>{item.name}</Title>
        <Author>{item.author.name}</Author>
        <Box display="flex" sx={{ alignItems: 'center' }}>
          <Typography variant="body2" color="text.secondary">
            {parseFloat(String(item?.rated || 0)).toFixed(2)}{' '}
            <LineIcon icon="ico-star" />
          </Typography>
          <Box display="flex" sx={{ alignItems: 'center', pl: 2 }}>
            {discount && (
              <Typography
                sx={{ textDecoration: 'line-through' }}
                color="text.hint"
                variant="body1"
              >
                ${price}
              </Typography>
            )}
            <Price variant="body2" sx={{ pl: 0.5 }}>
              {Number(price)
                ? `$${Number(discount || price)}`
                : i18n.formatMessage({ id: 'free' })}
            </Price>
          </Box>
        </Box>
      </RouteLink>
    </Box>
  );
}
