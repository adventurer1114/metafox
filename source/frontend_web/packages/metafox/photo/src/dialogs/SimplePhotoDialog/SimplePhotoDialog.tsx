/**
 * @type: dialog
 * name: photo.dialog.simplePhoto
 */
import { useGlobal } from '@metafox/framework';
import { Dialog, DialogContent } from '@metafox/dialog';
import { LineIcon } from '@metafox/ui';
import { styled } from '@mui/material';
import React from 'react';

const IconClose = styled('div')(({ theme }) => ({
  position: 'fixed',
  top: theme.spacing(4),
  right: theme.spacing(4),
  cursor: 'pointer',
  width: theme.spacing(5),
  height: theme.spacing(5),
  fontSize: theme.mixins.pxToRem(18),
  color:
    theme.palette.mode === 'light' ? theme.palette.background.paper : '#fff',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  zIndex: '2'
}));
const RootDialogContent = styled(DialogContent)(({ theme }) => ({
  padding: '0 !important',
  paddingTop: '0 !important',
  display: 'flex',
  overflowY: 'visible',
  zIndex: '1',
  flexFlow: 'column'
}));

const DialogImage = styled('div')(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  position: 'relative'
}));

const ImgStyled = styled('img')(({ theme }) => ({
  maxHeight: ' calc(100vh - 88px)',
  margin: 'auto',
  maxWidth: '1280px',
  borderRadius: '8px'
}));

export type SimplePhotoDialogProps = {
  src: string;
  alt: string;
  id: number | string;
};

export default function SimplePhotoDialog(props: SimplePhotoDialogProps) {
  const { useDialog } = useGlobal();
  const { src } = props;
  const { dialogProps, closeDialog } = useDialog();

  return (
    <Dialog {...dialogProps} data-testid="popupViewPhoto">
      <IconClose onClick={closeDialog}>
        <LineIcon icon="ico-close" />
      </IconClose>
      <RootDialogContent dividers={false}>
        <DialogImage>
          <ImgStyled src={src} alt="" style={{ width: '100%' }} />
        </DialogImage>
      </RootDialogContent>
    </Dialog>
  );
}
