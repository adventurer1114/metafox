/**
 * @type: block
 * name: core.block.offline
 * bundle: web
 * experiment: true
 */

import { createBlock, useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import { Box, Divider, Typography } from '@mui/material';
import React from 'react';

function Offline() {
  const { getSetting } = useGlobal();
  const message = getSetting('core.offline_message');

  return (
    <Block>
      <BlockContent>
        <Box
          sx={{
            maxWidth: 480,
            margin: '10% auto auto auto',
            background: 'white',
            borderRadius: 1,
            padding: 3
          }}
        >
          {message ? (
            <HtmlViewer html={message} />
          ) : (
            <>
              <Typography paragraph component="h1" variant="h4">
                Website is currently down for maintenance.
              </Typography>
              <Divider />
              <Typography paragraph variant="body1" sx={{ pt: 2 }}>
                [offline message here]
              </Typography>
            </>
          )}
        </Box>
      </BlockContent>
    </Block>
  );
}

export default createBlock({
  extendBlock: Offline,
  overrides: {
    title: 'Offline'
  }
});
