import React from 'react';
import HtmlViewer from '@metafox/html-viewer';
import { Box, Typography } from '@mui/material';
import { ProductContext } from '../AdminAppStoreShowDetail';

const Installation = () => {
  const item = React.useContext(ProductContext);

  return (
    <Box>
      <Typography variant="body2" color="text.secondary">
        <HtmlViewer html={item?.text_installation} />
      </Typography>
    </Box>
  );
};

export default Installation;
