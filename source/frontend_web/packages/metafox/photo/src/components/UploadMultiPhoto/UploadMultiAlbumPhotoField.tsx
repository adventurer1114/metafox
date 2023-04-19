/**
 * @type: formElement
 * name: form.element.UploadMultiAlbumItem
 */

import { FormFieldProps } from '@metafox/form';
import ErrorMessage from '@metafox/form-elements/ErrorMessage';
import { BasicFileItem, useGlobal } from '@metafox/framework';
import { DropFileBox, LineIcon } from '@metafox/ui';
import { isVideoType } from '@metafox/utils';
import { Button, FormControl, Grid, Tooltip, Typography } from '@mui/material';
import { styled } from '@mui/material/styles';
import { useField } from 'formik';
import { camelCase, isEmpty } from 'lodash';
import React, { useCallback } from 'react';
import PreviewImageComponent from './PreviewImage';
import useCheckMediaFileSize from '@metafox/photo/hooks/useCheckMediaFileSize';

const Control = styled('div', {
  name: 'MultipleUploadField',
  slot: 'Control',
  shouldForwardProp: prop => prop !== 'haveError'
})<{ haveError: boolean }>(({ theme, haveError }) => ({
  height: 120,
  borderRadius: 4,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  border: theme.mixins.border('secondary'),
  borderColor:
    theme.palette.mode === 'light' ? '#0000003b' : 'rgba(255, 255, 255, 0.23)',
  '& .ico-photos-plus-o': {
    fontSize: `${theme.mixins.pxToRem(15)} !important`
  },
  '&:hover': {
    borderColor: theme.palette.mode === 'light' ? '#000' : '#fff'
  },
  '& button': {
    [theme.breakpoints.down('sm')]: {
      flexDirection: 'column',
      height: 'auto',
      maxWidth: 'calc(100% - 40px)',
      padding: '10px'
    }
  },
  ...(haveError && {
    borderColor: `${theme.palette.error.main} !important`
  })
}));

export interface DropButtonProps {
  isOver?: boolean;
}

const DropButton = styled(Button, {
  name: 'DropButton',
  slot: 'DropButton'
})<DropButtonProps>(({ theme, isOver }) => ({
  ...(isOver && {
    backgroundColor: theme.palette.action.hover
  })
}));

const PreviewItem = styled('div', {
  name: 'MultipleUploadField',
  slot: 'PreviewItem'
})(({ theme }) => ({
  width: '100%',
  paddingBottom: '56%',
  borderRadius: 8,
  background: theme.palette.grey['A700'],
  position: 'relative',
  overflow: 'hidden'
}));

const PreviewVideo = styled('video', {
  name: 'MultipleUploadField',
  slot: 'PreviewVideo'
})({
  position: 'absolute',
  left: 0,
  top: 0,
  right: 0,
  bottom: 0,
  backgroundRepeat: 'no-repeat',
  backgroundPosition: 'center',
  maxWidth: '100%'
});

const RemoveBtn = styled('div', {
  name: 'MultipleUploadField',
  slot: 'removeBtn'
})(({ theme }) => ({
  width: theme.spacing(3),
  height: theme.spacing(3),
  borderRadius: theme.spacing(1.5),
  backgroundColor: 'rgba(0,0,0,0.89)',
  color: '#fff',
  position: 'absolute',
  top: theme.spacing(2),
  right: theme.spacing(2),
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  cursor: 'pointer'
}));
const AddMoreBtnWrapper = styled('div', {
  name: 'MultipleUploadField',
  slot: 'AddMoreBtnWrapper'
})(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  border: theme.mixins.border('primary'),
  borderRadius: theme.shape.borderRadius,
  height: '100%'
}));

const PreviewVideoWrapper = styled('div', {
  name: 'MultipleUploadField',
  slot: 'PreviewVideoWrapper'
})(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  height: '100%'
}));

const MaskPlay = styled('div', {
  name: 'MultipleUploadField',
  slot: 'MaskPlay'
})(({ theme }) => ({
  position: 'absolute',
  width: theme.spacing(5),
  height: theme.spacing(5),
  color: '#fff',
  backgroundColor: 'rgba(0,0,0,0.4)',
  borderRadius: '50%',
  left: '50%',
  top: '50%',
  marginLeft: theme.spacing(-2.5),
  marginTop: theme.spacing(-2.5),
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  fontSize: theme.mixins.pxToRem(24)
}));

export default function UploadMultiPhotoField({
  name,
  formik,
  config
}: FormFieldProps) {
  const {
    upload_url,
    max_upload_filesize: maxSizeLimit,
    allowUploadItems = true,
    allowRemoveItems = true
  } = config;
  const { i18n, dialogBackend } = useGlobal();
  const [, , { setValue: setFileItemTypeValue }] = useField('fileItemType');
  const [fieldItemPhoto, meta, { setValue, setTouched }] = useField(
    name ?? 'ItemPhotoField'
  );

  const [validFileItems, setValidFileItems, handleProcessFiles] =
    useCheckMediaFileSize({
      initialValues: fieldItemPhoto.value || [],
      upload_url,
      maxSizeLimit
    });

  const placeholder = config.placeholder || 'add_photo';

  const removeFile = (items: BasicFileItem[], item: BasicFileItem) => {
    const newItems = items.filter(x => x.uid !== item.uid || x.id !== item.id);
    setValidFileItems(newItems);
  };

  React.useEffect(() => {
    if (isEmpty(validFileItems)) setFileItemTypeValue(undefined);

    if (!meta.touched) {
      setTouched(true);
    }

    setValue(validFileItems);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [validFileItems]);

  const onDnDFile = files => {
    if (!files.length) return;

    handleProcessFiles(files);
  };

  const handleChoosePhoto = useCallback(() => {
    dialogBackend.present({
      component: 'photo.dialog.ChooseAlbumItemDialog',
      props: {
        config,
        fileItems: validFileItems,
        setFileItems: setValidFileItems,
        formik
      }
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [validFileItems]);

  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  let isShowDropBox = allowUploadItems;

  if (validFileItems.length) {
    isShowDropBox = true;
  }

  return (
    <>
      {isShowDropBox ? (
        <FormControl error={haveError} fullWidth margin="normal">
          <Typography sx={{ fontSize: '13px' }} color="text.hint" mb={1}>
            {config.label}
          </Typography>
          {fieldItemPhoto.value?.length ? null : (
            <DropFileBox
              onDrop={files => onDnDFile(files)}
              render={({ canDrop, isOver }) => (
                <Control
                  haveError={haveError}
                  role="button"
                  onClick={handleChoosePhoto}
                >
                  <DropButton
                    size="small"
                    color="primary"
                    isOver={isOver}
                    variant="outlined"
                    data-testid={camelCase(`field ${name}`)}
                    startIcon={<LineIcon icon="ico-photos-plus-o" />}
                  >
                    {i18n.formatMessage({ id: placeholder })}
                  </DropButton>
                </Control>
              )}
            />
          )}
        </FormControl>
      ) : null}
      <Grid container columnSpacing={1} rowSpacing={1}>
        {allowUploadItems && validFileItems.length ? (
          <Grid item sm={6} md={3} xs={6}>
            <DropFileBox
              style={{ height: '100%' }}
              onDrop={files => onDnDFile(files)}
              render={({ canDrop, isOver }) => (
                <AddMoreBtnWrapper>
                  <DropButton
                    size="large"
                    color="primary"
                    isOver={isOver}
                    startIcon={<LineIcon icon="ico-photos-plus-o" />}
                    sx={{ fontWeight: 'bold' }}
                    onClick={handleChoosePhoto}
                  >
                    {config?.label}
                  </DropButton>
                </AddMoreBtnWrapper>
              )}
            />
          </Grid>
        ) : null}
        {validFileItems?.map((item, index) => {
          return (
            <Grid item key={index} sm={6} md={3} xs={6}>
              <PreviewItem>
                {isVideoType(item?.file?.type) ? (
                  <PreviewVideoWrapper>
                    <PreviewVideo
                      src={item?.source}
                      controls={false}
                    ></PreviewVideo>
                    <MaskPlay>
                      <LineIcon icon="ico-play" />
                    </MaskPlay>
                  </PreviewVideoWrapper>
                ) : (
                  <PreviewImageComponent item={item} />
                )}
                {allowRemoveItems ? (
                  <Tooltip title={i18n.formatMessage({ id: 'remove' })}>
                    <RemoveBtn onClick={() => removeFile(validFileItems, item)}>
                      <LineIcon icon="ico-close" />
                    </RemoveBtn>
                  </Tooltip>
                ) : null}
              </PreviewItem>
            </Grid>
          );
        })}
      </Grid>
      {haveError ? <ErrorMessage error={meta.error} /> : null}
    </>
  );
}
