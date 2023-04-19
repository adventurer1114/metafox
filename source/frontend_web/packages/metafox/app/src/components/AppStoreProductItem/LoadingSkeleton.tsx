/**
 * @type: skeleton
 * name: app_store_product.itemView.mainCard.skeleton
 */
import { Box, Skeleton } from '@mui/material';
import React from 'react';

const LoadingSkeleton = () => {
  return (
    <Box
      sx={{
        width: '33%',
        padding: '15px 20px',
        color: '#555',
        height: '250px'
      }}
    >
      <Skeleton variant="rectangular" height="100%" />
    </Box>
  );
};

export default LoadingSkeleton;
