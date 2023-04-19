/**
 * @type: formElement
 * name: form.element.RawFile
 * chunkName: formElement
 */

import { useGlobal } from '@metafox/framework';
import { FormFieldProps } from '@metafox/form';
import { LineIcon } from '@metafox/ui';
import { shortenFileName, parseFileSize } from '@metafox/utils';
import { Button, FormControl, styled } from '@mui/material';
import { useField } from 'formik';
import { camelCase, uniqueId } from 'lodash';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import ErrorMessage from '../ErrorMessage';
import useStyles from './RawFileField.styles';

// apply this style help automation ci works property
const fixInputStyle: React.CSSProperties = {
  width: 2,
  position: 'absolute',
  right: 0,
  opacity: 0
};

export interface AttachmentItemProps {
  file_name: string;
  is_video?: boolean;
  is_image?: boolean;
  download_url: string;
  id?: number;
  file: File;
  key: string;
  fileItemType: string;
  classes?: Record<'item' | 'itemInfo', string>;
  handleDelete?: (id: number) => void;
  index?: number;
}

const AttachmentIcon = styled(LineIcon, { name: 'AttachmentIcon' })(
  ({ theme }) => ({
    paddingRight: theme.spacing(0.5),
    fontSize: theme.mixins.pxToRem(15)
  })
);

const AttachmentAction = styled('div', { name: 'AttachmentAction' })(
  ({ theme }) => ({
    color: theme.palette.primary.main,
    marginLeft: theme.spacing(1.5),
    cursor: 'pointer',
    '&:hover': {
      textDecoration: 'underline'
    }
  })
);

export default function AttachmentField({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const { dialogBackend, i18n } = useGlobal();
  const [field, meta, { setValue }] = useField(name ?? 'rawFile');

  const {
    item_type,
    fullWidth = true,
    margin = 'normal',
    size,
    max_upload_filesize,
    disabled,
    accept
  } = config;

  const [file, setFile] = useState<AttachmentItemProps>(field.value);

  const classes = useStyles();
  const inputRef = useRef<HTMLInputElement>();
  const placeholder = config.placeholder || 'Attach File';

  useEffect(() => {
    setValue(file);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [file]);

  const handleResetValue = (
    event: React.MouseEvent<HTMLInputElement, MouseEvent>
  ) => {
    event.currentTarget.value = null;
  };

  const handleDelete = useCallback(
    () => {
      dialogBackend
        .confirm({
          message: i18n.formatMessage({
            id: 'are_you_sure_you_want_to_delete_attachment_file'
          }),
          title: i18n.formatMessage({ id: 'are_you_sure' })
        })
        .then(oke => {
          if (!oke) return;

          setFile(null);
        });
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  const handleInputChange = useCallback(() => {
    const file = inputRef.current.files;

    if (file.length === 0) return;

    const fileItem: AttachmentItemProps = {
      file_name: file.item(0).name,
      download_url: URL.createObjectURL(file.item(0)),
      file: file.item(0),
      key: uniqueId('file'),
      fileItemType: item_type
    };

    const fileItemSize = fileItem.file.size;
    const fileItemName = fileItem.file_name;

    if (fileItemSize > max_upload_filesize && max_upload_filesize !== 0) {
      dialogBackend.alert({
        message: i18n.formatMessage(
          { id: 'warning_upload_limit_one_file' },
          {
            fileName: shortenFileName(fileItemName, 30),
            fileSize: parseFileSize(fileItem.file.size),
            maxSize: parseFileSize(max_upload_filesize)
          }
        )
      });

      return;
    }

    if (fileItem) {
      setFile(fileItem);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [file]);

  const handleControlClick = useCallback(() => {
    inputRef.current.click();
  }, []);

  return (
    <FormControl
      fullWidth={fullWidth}
      margin={margin}
      size={size}
      data-testid={camelCase(`field ${name}`)}
    >
      {!file ? (
        <div>
          <Button
            variant="outlined"
            size="small"
            color="primary"
            data-testid={camelCase(`button ${name}`)}
            onClick={handleControlClick}
            disabled={disabled || forceDisabled || formik.isSubmitting}
            startIcon={<LineIcon icon="ico-paperclip-alt" />}
          >
            {placeholder}
          </Button>
        </div>
      ) : (
        <div className={classes.item}>
          <div className={classes.itemInfo}>
            <AttachmentIcon icon="ico-paperclip-alt" />
            <div>{file.file_name}</div>
          </div>
          <AttachmentAction onClick={() => handleDelete()}>
            {i18n.formatMessage({ id: 'remove' })}
          </AttachmentAction>
        </div>
      )}

      {meta.error && <ErrorMessage error={meta.error} />}
      <input
        onClick={handleResetValue}
        data-testid={camelCase(`input ${name}`)}
        ref={inputRef}
        style={fixInputStyle}
        accept={accept}
        aria-hidden
        type="file"
        multiple={false}
        onChange={handleInputChange}
      />
    </FormControl>
  );
}
