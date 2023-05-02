/**
 * @type: dialog
 * name: dialog.sticker.detail
 */

import { Dialog, DialogTitle, DialogContent } from '@metafox/dialog';
import { styled, Box, Button } from '@mui/material';
import React from 'react';
import { StickerSetShape } from '@metafox/sticker';
import { useGetItem, useGlobal } from '@metafox/framework';
import StickerItem from './StickerItem';
import { getImageSrc } from '@metafox/utils';

const DialogContentStyled = styled(DialogContent, {
  name: 'DialogContentStyled'
})(({ theme }) => ({
  padding: `${theme.spacing(0, 1, 0, 2)}!important`,
  height: '50vh'
}));

const TabImg = styled('img', { name: 'StickerPicker', slot: 'tabImg' })(
  ({ theme }) => ({
    height: theme.spacing(16),
    maxWidth: theme.spacing(20)
  })
);

const TitleSticker = styled(Box, { name: 'titleSticker' })(({ theme }) => ({
  color: theme.palette.text.primary,
  fontSize: theme.mixins.pxToRem(18),
  marginBottom: theme.spacing(1.5),
  fontWeight: theme.typography.fontWeightSemiBold
}));

const TotalSticker = styled(Box, { name: 'totalSticker' })(({ theme }) => ({
  color: theme.palette.text.secondary,
  fontSize: theme.mixins.pxToRem(13),
  marginBottom: theme.spacing(2)
}));

const InfoSticker = styled(Box, { name: 'infoSticker' })(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  borderBottom: `1px solid ${theme.palette.divider}`,
  paddingBottom: theme.spacing(2)
}));

export default function ManagerStickerDialog({ identity, addToMyList, 
  removeToMyList }) {
  const stickerSet = useGetItem<StickerSetShape>(identity);
  const { i18n, useDialog } = useGlobal();
  const { dialogProps } = useDialog();

  const { stickers, image, title, statistic, is_added } = stickerSet;

  return (
    <Dialog {...dialogProps} maxWidth="sm" fullWidth>
      <DialogTitle enableBack>
        {i18n.formatMessage({ id: 'sticker_detail' })}
      </DialogTitle>
      <DialogContentStyled variant="fitScroll">
        <InfoSticker>
          <TabImg
            draggable={false}
            alt={title}
            src={getImageSrc(image, '200')}
          />
          <Box ml={2} mt={2}>
            <TitleSticker>{title}</TitleSticker>
            <TotalSticker>
              {i18n.formatMessage(
                { id: 'total_stickers' },
                { value: statistic.total_sticker }
              )}
            </TotalSticker>
            {is_added ? (
              <Button variant="outlined" size="medium" onClick={removeToMyList}>
                {i18n.formatMessage({ id: 'remove' })}
              </Button>
            ) : (
              <Button variant="contained" size="medium" onClick={addToMyList}>
                {i18n.formatMessage({ id: 'add' })}
              </Button>
            )}
          </Box>
        </InfoSticker>
        <Box ml={5} sx={{ display: 'flex', flexWrap: 'wrap' }}>
          {stickers.map(sticker => (
            <StickerItem identity={sticker} key={sticker} />
          ))}
        </Box>
      </DialogContentStyled>
    </Dialog>
  );
}
