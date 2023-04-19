/**
 * @type: block
 * name: layout.block.UserBackground
 * title: Page Background
 * description: Background Image
 * keywords: general
 */

import { createBlock } from '@metafox/framework';
import { Box, styled } from '@mui/material';
import React from 'react';

const Root = styled(Box, {})<{ image: string }>(({ theme, image }) => ({
  position: 'fixed',
  top: '0',
  right: '0',
  bottom: '0',
  left: '0',
  overflow: 'hidden',
  zIndex: -100,
  transform: 'translateZ(0px)',
  backgroundImage: `url(${image})`,
  backgroundSize: 'cover'
}));

function Base() {
  const image = 'https://intranet.younetco.com/local/templates/bitrix24/themes/light/grass/grass.jpg';

  return <Root image={image}/>;
}

export default createBlock({
  extendBlock: Base
});
