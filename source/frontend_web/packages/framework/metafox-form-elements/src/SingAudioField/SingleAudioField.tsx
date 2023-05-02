/**
 * @type: formElement
 * name: form.element.SingleAudioField
 * chunkName: formElement
 */

import { FormFieldProps } from '@metafox/form';
import { BasicFileItem, useGlobal } from '@metafox/framework';
import { DropFileBox, InputNotched, LineIcon } from '@metafox/ui';
import { shortenFileName, parseFileSize } from '@metafox/utils';
import {
  Box,
  Button,
  FormControl,
  InputLabel,
  Typography
} from '@mui/material';
import { styled } from '@mui/material/styles';
import { useField } from 'formik';
import { camelCase, uniqueId } from 'lodash';
import React, { useRef } from 'react';
import ErrorMessage from '../ErrorMessage';

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

const AudioUploaded = styled('div', {
  name: 'AudioUploaded'
})<{}>(({ theme }) => ({
  display: 'flex',
  fontSize: theme.mixins.pxToRem(13),
  '& .ico-music-note-o': {
    fontSize: theme.mixins.pxToRem(15)
  }
}));

const DropzoneBox = styled('div', {
  name: 'DropzoneBox'
})<{}>(({ theme }) => ({
  width: 'fit-content'
}));

const Label = styled(InputLabel, {
  name: 'Label'
})<{ haveError?: boolean }>(({ theme, haveError }) => ({
  ...(haveError && {
    color: theme.palette.error.main
  })
}));

export default function SingleAudioField({
  name,
  config,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const {
    label = 'Select Audio',
    description,
    item_type,
    accept = 'audio/*',
    max_upload_filesize,
    upload_url,
    disabled,
    storage_id
  } = config;
  // eslint-disable-next-line @typescript-eslint/no-unused-vars
  const { dialogBackend, i18n } = useGlobal();
  const [field, meta, { setValue }] = useField(name ?? 'ItemPhotoField');
  const [showVideoName, setShowVideoName] = React.useState<boolean>(false);
  const inputRef = useRef<HTMLInputElement>();

  const handleControlClick = () => {
    inputRef.current.click();
  };

  const handleRemoveVideo = () => {
    setShowVideoName(false);
    setValue(undefined);
  };

  const handleFile = file => {
    const fileItem: BasicFileItem = {
      id: 0,
      uid: uniqueId('file'),
      source: URL.createObjectURL(file),
      file,
      file_name: file.name,
      file_type: file.type,
      file_size: file.size,
      fileItemType: item_type,
      upload_url,
      status: 'create',
      storage_id: storage_id ?? null
    };

    const fileItemSize = fileItem.file_size;

    if (
      fileItemSize > max_upload_filesize?.music &&
      max_upload_filesize?.music
    ) {
      dialogBackend.alert({
        message: i18n.formatMessage(
          { id: 'warning_upload_limit_one_file' },
          {
            fileName: shortenFileName(fileItem.file_name, 30),
            fileSize: parseFileSize(fileItem.file_size),
            maxSize: parseFileSize(max_upload_filesize?.music)
          }
        )
      });

      return;
    }

    if (fileItem) {
      setValue(fileItem);
      setShowVideoName(true);
    }
  };

  const handleInputChange = () => {
    const file = inputRef.current.files;

    if (!file) return;

    handleFile(file.item(0));
  };

  const onDnDFile = files => {
    if (!files) return;

    const file = files[0];

    handleFile(file);
  };

  const handleResetValue = (
    event: React.MouseEvent<HTMLInputElement, MouseEvent>
  ) => {
    event.currentTarget.value = null;
  };

  const haveErr = !!(meta.error && (meta.touched || formik.submitCount));

  return (
    <FormControl
      fullWidth
      margin="normal"
      data-testid={camelCase(`field ${name}`)}
    >
      <Label haveError={haveErr} required variant="outlined" shrink="true">
        {config.label}
      </Label>
      <Box sx={{ p: 2, pt: 2.5 }}>
        <Typography
          variant="body2"
          color="text.secondary"
          mb={1.5}
          component="p"
        >
          {showVideoName
            ? i18n.formatMessage({ id: 'audio_file_selected' })
            : description}
        </Typography>
        {showVideoName ? (
          <AudioUploaded>
            <LineIcon icon="ico-music-note-o" />
            <Box ml={1} mr={1}>
              {shortenFileName(field.value?.file?.name, 30)}
            </Box>
            <Typography
              variant="body2"
              color="primary"
              component="span"
              role="button"
              onClick={handleRemoveVideo}
            >
              {i18n.formatMessage({ id: 'remove' })}
            </Typography>
          </AudioUploaded>
        ) : (
          <DropzoneBox>
            <DropFileBox
              onDrop={files => onDnDFile(files)}
              render={({ canDrop, isOver }) => (
                <DropButton
                  onClick={handleControlClick}
                  size="small"
                  color="primary"
                  sx={{ fontWeight: 'bold' }}
                  isOver={isOver}
                  variant="outlined"
                  disabled={disabled || forceDisabled || formik.isSubmitting}
                  data-testid={camelCase(`button ${name}`)}
                  startIcon={<LineIcon icon="ico-upload" />}
                >
                  {label}
                </DropButton>
              )}
            />
          </DropzoneBox>
        )}
        {haveErr ? <ErrorMessage error={meta.error} /> : null}
      </Box>
      <InputNotched
        haveError={haveErr}
        children={config.label}
        variant="outlined"
      />
      <input
        ref={inputRef}
        onClick={handleResetValue}
        className="srOnly"
        type="file"
        data-testid={camelCase(`input ${name}`)}
        multiple={false}
        accept={accept}
        onChange={handleInputChange}
      />
    </FormControl>
  );
}
