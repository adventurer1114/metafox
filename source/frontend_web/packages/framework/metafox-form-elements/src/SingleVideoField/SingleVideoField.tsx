/**
 * @type: formElement
 * name: form.element.SingleVideoFile
 * chunkName: formElement
 */

import { FormFieldProps } from '@metafox/form';
import { BasicFileItem, useGlobal } from '@metafox/framework';
import { DropFileBox, LineIcon } from '@metafox/ui';
import { shortenFileName, parseFileSize } from '@metafox/utils';
import { Box, Button, FormControl, Typography } from '@mui/material';
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

const VideoUploaded = styled('div', {
  name: 'VideoUploaded'
})<{}>(({ theme }) => ({
  display: 'flex',
  fontSize: theme.mixins.pxToRem(13),
  '& .ico-video': {
    fontSize: theme.mixins.pxToRem(15)
  }
}));

const DropzoneBox = styled('div', {
  name: 'DropzoneBox'
})<{}>(({ theme }) => ({
  width: 'fit-content'
}));

export default function SingleVideoField({
  name,
  config,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const {
    label = 'Select Video',
    description,
    item_type,
    accept = 'video/*',
    max_upload_filesize,
    upload_url,
    disabled
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
      status: 'create'
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
      <Typography variant="body2" color="text.secondary" mb={1.5} component="p">
        {showVideoName
          ? i18n.formatMessage({ id: 'video_file_selected' })
          : description}
      </Typography>
      {showVideoName ? (
        <VideoUploaded>
          <LineIcon icon="ico-video" />
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
        </VideoUploaded>
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
