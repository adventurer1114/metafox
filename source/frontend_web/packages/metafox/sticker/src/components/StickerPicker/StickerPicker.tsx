import { useGlobal } from '@metafox/framework';
import { ScrollContainer } from '@metafox/layout';
import { OnStickerClick } from '@metafox/sticker';
import {
  useMyStickerSet,
  useStickerSets,
  useMyStickerRecent
} from '@metafox/sticker/hooks';
import { LineIcon } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, Paper, Tooltip, IconButton, Typography } from '@mui/material';
import { styled } from '@mui/material/styles';
import React from 'react';
import StickerList from './StickerList';

const Tabs = styled('div', { name: 'StickerPicker', slot: 'Tabs' })(
  ({ theme }) => ({
    display: 'flex',
    flexDirection: 'row',
    borderTop: '1px solid',
    borderColor: theme.palette.border?.secondary
  })
);

const Tab = styled('div', {
  name: 'StickerPicker',
  slot: 'Tab',
  shouldForwardProp: prop => prop !== 'active'
})<{ active?: boolean }>(({ theme, active }) =>
  Object.assign(
    {
      height: 40,
      width: 40,
      fontSize: 16,
      padding: theme.spacing(0, 1, 0, 1),
      alignItems: 'center',
      justifyContent: 'center',
      display: 'flex',
      borderTop: '2px solid',
      borderTopColor: 'transparent'
    },
    active && {
      borderTopColor: theme.palette.primary.main
    }
  )
);

const TabImg = styled('img', { name: 'StickerPicker', slot: 'tabImg' })(
  ({ theme }) => ({
    height: 24,
    maxWidth: 32
  })
);

const NavStickerStyled = styled('div', { name: 'NavStickerStyled' })(
  ({ theme }) => ({
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center'
  })
);

const EmptySticker = styled(Box, { name: 'EmptySticker' })(({ theme }) => ({
  display: 'flex',
  flexDirection: 'column',
  color: theme.palette.text.secondary,
  alignItems: 'center',
  justifyContent: 'center',
  height: '100%'
}));

const IconEmpty = styled(LineIcon, { name: 'iconEmpty' })(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(26),
  marginBottom: theme.spacing(1)
}));

interface Props {
  multiple?: boolean;
  onStickerClick: OnStickerClick;
}

export default function StickerPicker({ onStickerClick }: Props) {
  const { dispatch, i18n } = useGlobal();
  const [activeTab, setActiveTab] = React.useState<number>(-1);
  const myStickerSet = useMyStickerSet();
  const stickerSets = useStickerSets(myStickerSet.data);
  const myStickerRecent = useMyStickerRecent();

  React.useEffect(() => {
    dispatch({ type: 'sticker/openStickerPickerDialog' });

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const changeTab = React.useCallback((index: number) => {
    setActiveTab(index);
  }, []);

  const openDialog = () => {
    dispatch({ type: 'sticker/openDialogSticker' });
  };

  return (
    <Paper
      sx={{
        width: 272,
        height: 280,
        display: 'flex',
        flexDirection: 'column',
        zIndex: 1400,
        bgcolor: 'background.paper',
        paddingTop: 1
      }}
      data-testid="popupStickerPicker "
    >
      <Box sx={{ flex: 1, overflowY: 'hidden' }}>
        <ScrollContainer autoHeightMax={240}>
          {activeTab === -1 ? (
            myStickerRecent?.data.length ? (
              <StickerList
                onStickerClick={onStickerClick}
                data={myStickerRecent?.data}
                key={activeTab.toString()}
              />
            ) : (
              <EmptySticker>
                <IconEmpty icon="ico-sticker" />
                <Typography>
                  {i18n.formatMessage({ id: 'no_sticker_used_yet' })}
                </Typography>
              </EmptySticker>
            )
          ) : (
            <StickerList
              onStickerClick={onStickerClick}
              identity={stickerSets[activeTab]._identity}
              key={activeTab.toString()}
            />
          )}
        </ScrollContainer>
      </Box>
      <NavStickerStyled>
        <Tabs>
          <Tab
            active={activeTab === -1}
            role="button"
            onClick={() => changeTab(-1)}
          >
            <Tooltip
              title={i18n.formatMessage({ id: 'recent_stickers' })}
              placement="bottom"
            >
              <LineIcon sx={{ fontSize: '1.125rem' }} icon="ico-clock-o" />
            </Tooltip>
          </Tab>
          {stickerSets.map((data, index) => (
            <Tab
              active={activeTab === index}
              role="button"
              onClick={() => changeTab(index)}
              key={index.toString()}
            >
              <Tooltip title={data.title} placement="bottom">
                <TabImg
                  draggable={false}
                  alt="tabItem"
                  height="24px"
                  src={getImageSrc(data?.image, '200')}
                />
              </Tooltip>
            </Tab>
          ))}
        </Tabs>
        <IconButton color="primary" onClick={openDialog}>
          <LineIcon sx={{ fontSize: '1.125rem' }} icon="ico-plus" />
        </IconButton>
      </NavStickerStyled>
    </Paper>
  );
}
