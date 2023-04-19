import React from 'react';
import HtmlViewer from '@metafox/html-viewer';
import { Box, Typography, styled } from '@mui/material';
import { ProductContext } from '../AdminAppStoreShowDetail';

const WhatNewsDescription = styled(Box, { name: 'whatNewsDescription' })(
  ({ theme }) => ({
    padding: theme.spacing(2),
    color: '#555',
    margin: theme.spacing(3, 0),
    position: 'relative'
  })
);

const Detail = () => {
  const item = React.useContext(ProductContext);

  return (
    <Box>
      <WhatNewsDescription>
        <HtmlViewer html={item?.description} />
        <Box
          sx={{
            position: 'absolute',
            left: 0,
            top: 0,
            width: '100%',
            height: '100%',
            backgroundColor: '#888',
            opacity: 0.1
          }}
        ></Box>
      </WhatNewsDescription>
      <Box>
        <Typography variant="body2" color="text.secondary">
          <HtmlViewer html={item?.text_detail} />
        </Typography>
      </Box>
    </Box>
  );
};

export default Detail;
