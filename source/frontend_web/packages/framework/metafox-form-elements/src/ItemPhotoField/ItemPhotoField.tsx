/**
 * @type: formElement
 * name: form.element.SinglePhotoFile
 * chunkName: formElement
 */

import { FormFieldProps } from '@metafox/form';
import { BasicFileItem, useGlobal } from '@metafox/framework';
import { InputNotched, LineIcon } from '@metafox/ui';
import {
  getFileExtension,
  parseFileSize,
  shortenFileName,
  isVideoType
} from '@metafox/utils';
import { Button, FormControl, InputLabel, styled } from '@mui/material';
import { useField } from 'formik';
import { camelCase, uniqueId } from 'lodash';
import React, { useRef, useState } from 'react';
import ErrorMessage from '../ErrorMessage';
// import Label from '../Label';
import useStyles from './ItemPhotoField.styles';

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

const readFile = (file: File) => {
  return new Promise(resolve => {
    const reader = new FileReader();
    reader.addEventListener('load', () => resolve(reader.result), false);
    reader.readAsDataURL(file);
  });
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
    thumbnail_sizes
  } = config;
  const classes = useStyles();
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const { dialogBackend, i18n } = useGlobal();
  const [, meta, { setValue }] = useField(name ?? 'ItemPhotoField');
  const placeholder = config.placeholder || 'add_photo';
  const [previewUrl, setPreviewUrl] = useState(initialPreviewURL);
  const [hasPreview, setHasPreview] = useState(Boolean(previewUrl));
  const inputRef = useRef<HTMLInputElement>();
  const [hoverState, setHoverState] = useState<boolean>(false);

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
        <FormControl
          className={classes.root}
          fullWidth
          variant={variant as any}
          margin="normal"
          data-testid={camelCase(`field ${name}`)}
        >
          <div className={classes.preview}>
            <div
              className={classes.previewImage}
              style={{ backgroundImage: `url(${previewUrl})` }}
              onMouseOver={() => setHoverState(true)}
              onMouseLeave={() => setHoverState(false)}
            />
            <div className={classes.actions}>
              <Button
                size="smaller"
                color="primary"
                variant="contained"
                disabled={disabled || forceDisabled || formik.isSubmitting}
                data-testid={camelCase(`button add ${name}`)}
                startIcon={<LineIcon icon="ico-photo-o" />}
                onClick={handleControlClick}
              >
                {i18n.formatMessage({ id: 'change' })}
              </Button>
              <Button
                size="smaller"
                color="error"
                data-testid={camelCase(`button remove ${name}`)}
                variant="contained"
                disabled={disabled || forceDisabled || formik.isSubmitting}
                startIcon={<LineIcon icon="ico-trash-o" />}
                onClick={handleDeletePhoto}
              >
                {i18n.formatMessage({ id: 'remove' })}
              </Button>
            </div>
          </div>
          <InputNotched
            haveError={haveError}
            variant={variant}
            children={null}
            hoverState={hoverState}
          />
        </FormControl>
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
      <FormControl
        className={classes.root}
        fullWidth
        variant={variant as any}
        margin="normal"
        data-testid={camelCase(`field ${name}`)}
      >
        <InputLabel
          required={required}
          className={classes.formLabel}
          variant={variant as any}
          shrink={variant === 'outlined'}
        >
          {config.label}
        </InputLabel>
        <div
          className={classes.controls}
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
        </div>
        <InputNotched
          haveError={haveError}
          children={config.label}
          variant={variant}
          hoverState={hoverState}
        />
      </FormControl>
      {haveError ? <ErrorMessage error={meta.error} /> : null}
      <input
        onClick={handleResetValue}
        ref={inputRef}
        type="file"
        aria-hidden
        accept="image/*"
        data-testid={camelCase(`input ${name}`)}
        onChange={handleInputChange}
        style={fixInputStyle}
      />
    </>
  );
}
