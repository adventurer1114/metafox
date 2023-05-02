/**
 * @type: formElement
 * name: form.element.SinglePhotoFile
 * chunkName: formElement
 */

import { FormFieldProps } from '@metafox/form';
import { BasicFileItem, useGlobal } from '@metafox/framework';
import { LineIcon, Image } from '@metafox/ui';
import {
  getFileExtension,
  parseFileSize,
  shortenFileName,
  isVideoType
} from '@metafox/utils';
import {
  Button,
  FormControl,
  Tooltip,
  styled,
  Link,
  Typography
} from '@mui/material';
import { useField } from 'formik';
import { camelCase, isEmpty, uniqueId } from 'lodash';
import React, { useRef, useState } from 'react';
import ErrorMessage from '../ErrorMessage';

// apply this style help automation ci works property
const fixInputStyle: React.CSSProperties = {
  width: 2,
  right: 0,
  position: 'absolute',
  opacity: 0
};

const AddPhotoButton = styled(Button, { name: 'AddPhotoButton' })(
  ({ theme }) => ({
    fontWeight: 'bold'
  })
);

const Root = styled(FormControl, { name: 'Root' })(({ theme }) => ({
  margin: theme.spacing(2, 0, 1)
}));

const Preview = styled('div', { name: 'Preview' })(({ theme }) => ({
  marginTop: -5,
  borderRadius: 4,
  width: '100%',
  maxWidth: '100%',
  overflow: 'hidden',
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  position: 'relative',
  '& button': {
    '& + button': {
      marginLeft: theme.spacing(1)
    }
  }
}));

const RemoveBtn = styled('div', {
  name: 'RemoveBtn',
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

const Controls = styled('div', {
  name: 'Controls',
  slot: 'Controls'
})(({ theme }) => ({
  borderRadius: 4,
  display: 'flex',
  alignItems: 'center',
  justifyContent: 'center',
  position: 'absolute',
  top: 0,
  left: 0,
  width: '100%',
  height: '100%'
}));

const ImageWrapper = styled('div', {
  name: 'ImageWrapper',
  slot: 'ImageWrapper',
  shouldForwardProp: prop => prop !== 'widthPhoto' && prop !== 'haveError'
})<{
  widthPhoto?: number;
  haveError?: boolean;
  hoverState?: boolean;
}>(({ theme, haveError, hoverState, widthPhoto }) => ({
  width: widthPhoto,
  maxWidth: widthPhoto,
  overflow: 'hidden',
  position: 'relative',
  borderStyle: 'solid',
  borderWidth: '1px',
  borderColor:
    theme.palette.mode === 'light' ? '#0000003b' : 'rgba(255, 255, 255, 0.23)',
  borderRadius: 4,
  ...(hoverState && {
    borderColor: theme.palette.mode === 'light' ? '#000' : '#fff'
  }),
  ...(haveError && {
    borderColor: theme.palette.error.main
  })
}));

const readFile = (file: File) => {
  return new Promise(resolve => {
    const reader = new FileReader();
    reader.addEventListener('load', () => resolve(reader.result), false);
    reader.readAsDataURL(file);
  });
};

const checkFileAcceptNoPass = (typeFile: any, accept: any) => {
  let result = true;

  if (isEmpty(typeFile)) return true;

  if (isEmpty(accept)) return false;

  const acceptData = accept.split(',');

  if (acceptData.some(item => typeFile.match(item))) {
    result = false;
  }

  return result;
};

export default function ItemPhotoField({
  name,
  config,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const {
    required,
    variant = 'outlined',
    item_type,
    max_upload_filesize,
    upload_url,
    preview_url: initialPreviewURL,
    disabled,
    returnBase64 = false,
    thumbnail_sizes,
    aspectRatio = '1:1',
    widthPhoto = '200px',
    accept = 'image/*'
  } = config;
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const { dialogBackend, i18n } = useGlobal();
  const [, meta, { setValue }] = useField(name ?? 'ItemPhotoField');
  const placeholder = config.placeholder || 'add_photo';
  const [previewUrl, setPreviewUrl] = useState(initialPreviewURL);
  const [hasPreview, setHasPreview] = useState(Boolean(previewUrl));
  const inputRef = useRef<HTMLInputElement>();
  const [hoverState, setHoverState] = useState<boolean>(false);
  const Ratio = aspectRatio.replace(':', '');

  const handleConvertBase64 = async (fileData: File, cb) => {
    if (fileData) {
      const base64 = await readFile(fileData);
      cb(base64);
    }
  };

  const handleControlClick = () => {
    inputRef.current.click();
  };

  const handleInputChange = () => {
    const file = inputRef.current.files;

    if (!file) return;

    const type = isVideoType(file.item(0).type) ? 'video' : 'photo';
    const fileItem: BasicFileItem = {
      id: 0,
      uid: uniqueId('file'),
      source: URL.createObjectURL(file.item(0)),
      file: file.item(0),
      file_name: file.item(0).name,
      file_size: file.item(0).size,
      file_type: type,
      fileItemType: item_type,
      status: 'update',
      upload_url,
      extension: getFileExtension(file.item(0).name),
      thumbnail_sizes
    };

    const fileItemSize = fileItem.file.size;

    if (fileItemSize > max_upload_filesize && max_upload_filesize !== 0) {
      dialogBackend.alert({
        message: i18n.formatMessage(
          { id: 'warning_upload_limit_one_file' },
          {
            fileName: shortenFileName(fileItem.file_name, 30),
            fileSize: parseFileSize(fileItem.file.size),
            maxSize: parseFileSize(max_upload_filesize)
          }
        )
      });

      return;
    }

    if (checkFileAcceptNoPass(fileItem?.file?.type, accept)) {
      dialogBackend.alert({
        message: i18n.formatMessage({ id: 'photo_accept_type_fail' })
      });

      if (inputRef?.current) {
        inputRef.current.value = null;
      }

      return;
    }

    if (fileItem) {
      setPreviewUrl(fileItem.source);
      setHasPreview(true);

      if (returnBase64) {
        handleConvertBase64(fileItem?.file, result => {
          setValue({ base64: result });
        });
      } else {
        setValue(fileItem);
      }
    }
  };

  const handleDeletePhoto = () => {
    setPreviewUrl(null);
    setHasPreview(false);
    setValue(
      initialPreviewURL ? { status: 'remove', temp_file: 0 } : undefined
    );
  };

  const handleResetValue = (
    event: React.MouseEvent<HTMLInputElement, MouseEvent>
  ) => {
    event.currentTarget.value = null;
  };

  const haveError = Boolean(meta.error && (meta.touched || formik.submitCount));

  if (hasPreview) {
    return (
      <>
        <Root
          fullWidth
          variant={variant as any}
          margin="normal"
          data-testid={camelCase(`field ${name}`)}
        >
          <Typography sx={{ fontSize: '13px' }} color="text.hint" mb={1}>
            {config.label}
            {required ? '(*)' : ''}
          </Typography>
          <ImageWrapper
            haveError={haveError}
            hoverState={hoverState}
            widthPhoto={widthPhoto}
          >
            <Preview
              onMouseOver={() => setHoverState(true)}
              onMouseLeave={() => setHoverState(false)}
            >
              <Image src={previewUrl} aspectRatio={Ratio} backgroundImage />
              <Tooltip title={i18n.formatMessage({ id: 'remove' })}>
                <RemoveBtn onClick={handleDeletePhoto}>
                  <LineIcon icon="ico-close" />
                </RemoveBtn>
              </Tooltip>
            </Preview>
          </ImageWrapper>
        </Root>
        <Link
          onClick={handleControlClick}
          color="primary"
          data-testid={camelCase(`button add ${name}`)}
          disabled={disabled || forceDisabled || formik.isSubmitting}
        >
          {i18n.formatMessage({ id: 'change' })}
        </Link>
        {haveError ? <ErrorMessage error={meta.error} /> : null}
        <input
          onClick={handleResetValue}
          ref={inputRef}
          type="file"
          aria-hidden
          accept="image/*"
          style={fixInputStyle}
          data-testid={camelCase(`input ${name}`)}
          onChange={handleInputChange}
        />
      </>
    );
  }

  return (
    <>
      <Root
        variant={variant as any}
        margin="normal"
        data-testid={camelCase(`field ${name}`)}
      >
        <Typography sx={{ fontSize: '13px' }} color="text.hint" mb={1}>
          {config.label}
          {required ? '(*)' : ''}
        </Typography>
        <ImageWrapper
          haveError={haveError}
          hoverState={hoverState}
          widthPhoto={widthPhoto}
        >
          <Image src={previewUrl} aspectRatio={Ratio} backgroundImage />
          <Controls
            role="button"
            onClick={handleControlClick}
            onMouseOver={() => setHoverState(true)}
            onMouseLeave={() => setHoverState(false)}
          >
            <AddPhotoButton
              size="small"
              color="primary"
              variant="outlined"
              data-testid={camelCase(`button add ${name}`)}
              disabled={disabled || forceDisabled || formik.isSubmitting}
              startIcon={<LineIcon icon="ico-photo-plus-o" />}
            >
              {i18n.formatMessage({ id: placeholder })}
            </AddPhotoButton>
          </Controls>
        </ImageWrapper>
      </Root>
      {haveError ? <ErrorMessage error={meta.error} /> : null}
      <input
        onClick={handleResetValue}
        ref={inputRef}
        type="file"
        aria-hidden
        accept={accept}
        data-testid={camelCase(`input ${name}`)}
        onChange={handleInputChange}
        style={fixInputStyle}
      />
    </>
  );
}
