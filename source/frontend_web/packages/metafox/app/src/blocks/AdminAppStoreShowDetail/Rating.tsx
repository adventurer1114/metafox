import * as React from 'react';
import { Rating, Typography, styled, Box } from '@mui/material';
import { useGlobal } from '@metafox/framework';
import { ProductContext } from './AdminAppStoreShowDetail';

const RatingWrapper = styled(Box, { name: 'versionWrapper' })(({ theme }) => ({
  border: 'solid 1px rgba(85, 85, 85, 0.1)',
  width: '370px',
  padding: theme.spacing(3)
}));

const RatingInfo = styled(Box, { name: 'versionInfo' })(({ theme }) => ({
  alignItems: 'center',
  justifyContent: 'space-between',
  height: 'fit-content',
  display: 'flex',
  '&:not(:first-of-type)': {
    paddingTop: theme.spacing(1)
  }
}));

export default function Rate() {
  const { i18n } = useGlobal();
  const item = React.useContext(ProductContext);

  return (
    <RatingWrapper>
      <RatingInfo>
        <Typography variant="subtitle2" color="text.secondary">
          {i18n.formatMessage({ id: 'rating' })}
        </Typography>
        <Rating
          name="rating"
          defaultValue={item?.rated}
          precision={0.1}
          readOnly
        />
      </RatingInfo>
      {parseInt(item?.total_rated, 10) > 0 ? (
        <RatingInfo>
          {i18n.formatMessage(
            { id: 'total_rated' },
            {
              rated: parseFloat(String(item?.rated || 0)).toFixed(2),
              total_rated: item?.total_rated
            }
          )}
        </RatingInfo>
      ) : null}
    </RatingWrapper>
  );
}
