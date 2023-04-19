/**
 * @type: block
 * name: install.UpgradeCompleted
 * bundle: installation
 */
import { createBlock } from '@metafox/framework';
import {
  Panel,
  PanelBody,
  PanelContent,
  PanelHeader
} from '@metafox/installation/components';
import { Alert, Box, Link, Typography } from '@mui/material';
import React from 'react';
import { useInstallationState } from '../hooks';

function Body() {
  const { baseUrl } = useInstallationState();

  return (
    <>
      <Typography variant="h3" paragraph>
        Upgrade Completed
      </Typography>
      <Alert severity="error">
        Warning: The installation path (public/install) is still accessible.
        Please remove public/install folder to avoid security risks.
      </Alert>
      <Box sx={{ mt: 2 }}>
        <Typography>
          {'Enjoy your new '}
          <Link href={`${baseUrl}/`} color="primary">
            Social Network
          </Link>
          {' or configure your site in the '}
          <Link href={`${baseUrl}/admincp`} color="primary">
            AdminCP
          </Link>
          {' now'}
        </Typography>
      </Box>
      <Box sx={{ mt: 4 }}>
        <Typography component="div" sx={{ alignSelf: 'flex-end' }}>
          Your MetaFox is installed successfully.
        </Typography>
        <Typography component="div">MetaFox Team</Typography>
      </Box>
    </>
  );
}

function UpgradeCompleted() {
  return (
    <Panel>
      <PanelHeader />
      <PanelBody>
        <PanelContent data-testid="blockInstallCompleted">
          <Body />
        </PanelContent>
      </PanelBody>
    </Panel>
  );
}

export default createBlock({
  extendBlock: UpgradeCompleted
});
