import React from 'react';
import HtmlViewer from '@metafox/html-viewer';
import { Box, Typography } from '@mui/material';
import { ProductContext } from '../AdminAppStoreShowDetail';

const Changelog = () => {
  const item = React.useContext(ProductContext);

  return (
    <Box>
      <Typography variant="body2" color="text.secondary">
        <HtmlViewer html={item?.text_changelog} />
      </Typography>
    </Box>
  );
};

export default Changelog;
