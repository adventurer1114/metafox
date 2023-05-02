/**
 * @type: ui
 * name: sticker.ui.stickerSet
 */

import { useGetItem, useGlobal } from '@metafox/framework';
import { Box, styled, Button } from '@mui/material';
import { StickerSetShape } from '@metafox/sticker';
import { getImageSrc } from '@metafox/utils';
import React from 'react';

interface Props {
  identity: string;
}
const TabImg = styled('img', { name: 'StickerPicker', slot: 'tabImg' })(
  ({ theme }) => ({
    height: theme.spacing(10),
    maxWidth: theme.spacing(10)
  })
);

const ItemView = styled(Box, { name: 'itemView' })(({ theme }) => ({
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'center',
  marginRight: theme.spacing(1.5),
  padding: theme.spacing(4, 0),
  borderBottom: `1px solid ${theme.palette.divider}`
}));

const InfoSticker = styled(Box, { name: 'infoSticker' })(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  cursor: 'pointer'
}));

const TitleSticker = styled(Box, { name: 'titleSticker' })(({ theme }) => ({
  color: theme.palette.text.primary,
  fontSize: theme.mixins.pxToRem(18),
  marginBottom: theme.spacing(1.5),
  fontWeight: theme.typography.fontWeightSemiBold
}));

const TotalSticker = styled(Box, { name: 'totalSticker' })(({ theme }) => ({
  color: theme.palette.text.secondary,
  fontSize: theme.mixins.pxToRem(13)
}));

export default function StickerSet({ identity }: Props) {
  const { i18n, dialogBackend, dispatch } = useGlobal();
  const item = useGetItem<StickerSetShape>(identity);
  const { image, is_added, statistic, title } = item;

  const openStickerDetail = () => {
    dialogBackend.present({
      component: 'dialog.sticker.detail',
      props: {
        identity,
        addToMyList,
        removeToMyList
      }
    });
  };

  const addToMyList = () => {
    dispatch({ type: 'sticker/addStickerSet', payload: { identity } });
  };

  const removeToMyList = () => {
    dispatch({ type: 'sticker/removeStickerSet', payload: { identity } });
  };

  return (
    <ItemView>
      <InfoSticker onClick={openStickerDetail}>
        <TabImg
          draggable={false}
          alt="tabItem"
          src={getImageSrc(image, '200')}
        />
        <Box ml={3}>
          <TitleSticker>{title}</TitleSticker>
          <TotalSticker>
            {i18n.formatMessage(
              { id: 'total_stickers' },
              { value: statistic.total_sticker }
            )}
          </TotalSticker>
        </Box>
      </InfoSticker>
      {is_added ? (
        <Button variant="outlined" size="medium" onClick={removeToMyList}>
          {i18n.formatMessage({ id: 'remove' })}
        </Button>
      ) : (
        <Button variant="contained" size="medium" onClick={addToMyList}>
          {i18n.formatMessage({ id: 'add' })}
        </Button>
      )}
    </ItemView>
  );
}
