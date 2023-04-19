/**
 * @type: formElement
 * name: form.element.SimpleUploadPhotos
 */

import { FormFieldProps } from '@metafox/form';
import { BasicFileItem, useGlobal } from '@metafox/framework';
import { usePageParams } from '@metafox/layout';
import { LineIcon } from '@metafox/ui';
import { Button, FormControl, styled } from '@mui/material';
import { useField } from 'formik';
import React, { useCallback } from 'react';

const name = 'simpleUploadPhotos';

const Root = styled('div', { name, slot: 'root' })(({ theme }) => ({
  display: 'flex',
  flexDirection: 'column',
  justifyContent: 'center',
  alignItems: 'center'
}));
const ButtonStyle = styled(Button, { name, slot: 'root' })(({ theme }) => ({
  fontWeight: theme.typography.fontWeightBold,
  fontSize: theme.typography.pxToRem(18)
}));
const ButtonWrap = styled('div', {
  name,
  slot: 'ButtonWrap',
  shouldForwardProp: prop => prop !== 'size' && prop !== 'isProfilePage'
})<{ size?: string; isProfilePage?: boolean }>(
  ({ theme, size, isProfilePage }) => ({
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    border: theme.mixins.border('primary'),
    height: theme.spacing(4.75),
    marginTop: theme.spacing(1),
    ...(size === 'small' && {
      borderRadius: theme.shape.borderRadius,
      padding: theme.spacing(0, 1)
    }),
    ...(size !== 'small' && {
      height: 125,
      width: '100%'
    }),
    ...(size !== 'small' && {
      height: isProfilePage ? 170 : 125,
      width: '100%'
    }),
    '&:hover': {
      cursor: 'pointer',
      backgroundColor: theme.palette.button.hover
    },
    '& .MuiButton-root.MuiButton-text:hover': {
      backgroundColor: 'transparent'
    }
  })
);

const FormControlStyled = styled(FormControl, {
  name,
  slot: 'FormControl',
  shouldForwardProp: prop => prop !== 'size'
})<{ size?: string }>(({ theme, size }) => ({
  ...(size !== 'small' && {
    marginTop: theme.spacing(0),
    marginBottom: theme.spacing(0)
  })
}));

export default function SimpleUploadPhotos({
  name,
  formik,
  config
}: FormFieldProps) {
  const { size = null } = config;
  const { i18n, dialogBackend } = useGlobal();
  const pageParams = usePageParams();
  const [, meta, { setValue }] = useField(name ?? 'simpleUploadPhotos');

  const [fileItems, setFileItems] = React.useState<BasicFileItem[]>([]);
  const [isProfilePage, setIsProfilePage] = React.useState(false);

  React.useEffect(() => {
    if (
      'user' === pageParams?.resource_name &&
      pageParams?.profile_page &&
      pageParams?.album_id
    ) {
      setIsProfilePage(true);
    }
  }, [pageParams]);

  const placeholder = config.placeholder || 'add_photos_video';

  React.useEffect(() => {
    setValue(fileItems);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [fileItems]);

  const handleChoosePhoto = useCallback(() => {
    dialogBackend.present({
      component: 'photo.dialog.ChooseAlbumItemDialog',
      props: {
        config,
        fileItems,
        setFileItems,
        isDetailAlbum: true,
        formik
      }
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  React.useEffect(() => {
    if (haveError) {
      dialogBackend.alert({
        message: meta.error
      });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [haveError, meta?.error, fileItems]);

  return (
    <FormControlStyled size={size} error={haveError} fullWidth margin="normal">
      <Root>
        <ButtonWrap
          size={size}
          isProfilePage={isProfilePage}
          onClick={handleChoosePhoto}
        >
          <ButtonStyle
            size="medium"
            color="primary"
            startIcon={<LineIcon icon="ico-photos-plus-o" />}
          >
            {i18n.formatMessage({ id: placeholder })}
          </ButtonStyle>
        </ButtonWrap>
      </Root>
    </FormControlStyled>
  );
}
