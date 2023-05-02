/**
 * @type: block
 * name: livestream.block.ShareLivestreamBlock
 * title: Share Livestream Form
 * keywords: livestream
 * description: Share Livestream Form
 * experiment: true
 */
import { RemoteFormBuilder } from '@metafox/form';
import {
  BlockViewProps,
  createBlock,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import { Block, BlockContent, BlockHeader, BlockTitle } from '@metafox/layout';
import { LineIcon } from '@metafox/ui';
import { Box, IconButton } from '@mui/material';
import { styled } from '@mui/material/styles';
import React from 'react';
import {
  APP_LIVESTREAM,
  RESOURCE_LIVE_VIDEO
} from '@metafox/livestreaming/constants';

const Tabs = styled('div', {
  name: 'Tab',
  slot: 'container'
})<{}>(({ theme }) => ({
  display: 'flex',
  flexDirection: 'row'
}));

const Tab = styled('div', {
  name: 'Tab',
  slot: 'item',
  shouldForwardProp: prop => prop !== 'active'
})<{ active?: boolean }>(({ theme, active }) => ({
  cursor: 'pointer',
  textTransform: 'uppercase',
  fontWeight: theme.typography.fontWeightBold,
  fontSize: theme.mixins.pxToRem(15),
  padding: theme.spacing(2, 0),
  marginRight: theme.spacing(3.75),
  color: theme.palette.text.secondary,
  borderBottom: 'solid 2px',
  borderBottomColor: 'transparent',
  ...(active && {
    color: theme.palette.primary.main,
    borderBottomColor: theme.palette.primary.main
  })
}));

const Panels = styled(Box, {
  name: 'Tab',
  slot: 'panels'
})<{}>(({ theme }) => ({}));

const Panel = styled(Box, {
  name: 'Tab',
  slot: 'panel'
})<{ active?: boolean }>(({ theme, active }) => ({
  display: active ? 'block' : 'none'
}));

const LivestreamForm = ({ name }: { name: string }) => {
  const dataSource = useResourceAction(
    APP_LIVESTREAM,
    RESOURCE_LIVE_VIDEO,
    name
  );

  return <RemoteFormBuilder noHeader dataSource={dataSource} />;
};

const BackButton = ({ icon = 'ico-arrow-left', ...restProps }) => {
  const { navigate } = useGlobal();

  const handleClick = () => {
    navigate(-1);
  };

  return (
    <IconButton
      size="small"
      role="button"
      id="back"
      data-testid="buttonBack"
      sx={{ transform: 'translate(-5px,0)' }}
      onClick={handleClick}
      {...restProps}
    >
      <LineIcon icon={icon} />
    </IconButton>
  );
};

function ShareLivestreamBlock({ title }: BlockViewProps) {
  const [tab, setTab] = React.useState<string>('software');
  const { i18n } = useGlobal();

  return (
    <Block>
      <BlockHeader>
        <BlockTitle>
          <BackButton />
          {i18n.formatMessage({ id: title })}
        </BlockTitle>
      </BlockHeader>
      <BlockContent>
        <Tabs>
          <Tab active={tab === 'software'} onClick={() => setTab('software')}>
            {i18n.formatMessage({ id: 'streaming_software' })}
          </Tab>
        </Tabs>
        <Panels>
          <Panel active={tab === 'software'}>
            <LivestreamForm name="addItem" />
          </Panel>
        </Panels>
      </BlockContent>
    </Block>
  );
}

export default createBlock({
  extendBlock: ShareLivestreamBlock,

  overrides: {
    noHeader: false
  }
});
