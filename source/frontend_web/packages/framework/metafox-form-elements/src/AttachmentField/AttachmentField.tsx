/**
 * @type: formElement
 * name: form.element.Attachment
 * chunkName: formExtras
 */

import { FormFieldProps } from '@metafox/form';
import { BasicFileItem, useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import {
  getFileExtension,
  shortenFileName,
  parseFileSize
} from '@metafox/utils';
import { Box, Button, FormControl, styled } from '@mui/material';
import { useField } from 'formik';
import produce from 'immer';
import { camelCase, get, uniqueId, isEqual } from 'lodash';
import React, { useCallback, useEffect, useRef, useState } from 'react';
import ErrorMessage from '../ErrorMessage';

// apply this style help automation ci works property
const fixInputStyle: React.CSSProperties = {
  width: 2,
  position: 'absolute',
  right: 0,
  opacity: 0
};

export interface AttachmentItemProps {
  item: BasicFileItem;
  error?: string;
  handleDelete?: (id: number) => void;
  index?: number;
}

const AttachmentIcon = styled(LineIcon, { name: 'AttachmentIcon' })(
  ({ theme }) => ({
    paddingRight: theme.spacing(0.5),
    fontSize: theme.mixins.pxToRem(15)
  })
);

const AttachmentButton = styled(Button, { name: 'AttachmentButton' })(
  ({ theme }) => ({
    fontWeight: 'bold'
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

const StyledAttachmentItem = styled(Box, {
  name: 'AttachmentItem',
  slot: 'FormItem'
})(({ theme }) => ({
  display: 'flex',
  marginTop: theme.spacing(1),
  fontSize: theme.mixins.pxToRem(13),
  lineHeight: 1.5,
  '& .info': {
    display: 'flex'
  }
}));

const StyledNameFile = styled('div')(({ theme }) => ({
  overflowWrap: 'anywhere'
}));

function AttachmentItem({
  item,
  index,
  handleDelete,
  error
}: AttachmentItemProps) {
  const { i18n } = useGlobal();

  // does not render item is removed
  // keep item to send to server
  if (item.status === 'remove') return null;

  return (
    <div>
      <StyledAttachmentItem>
        <div className="info">
          <AttachmentIcon icon="ico-paperclip-alt" />
          <StyledNameFile>{item.file_name}</StyledNameFile>
        </div>
        <AttachmentAction onClick={() => handleDelete(index)}>
          {i18n.formatMessage({ id: 'remove' })}
        </AttachmentAction>
      </StyledAttachmentItem>
      {error ? <ErrorMessage error={error} /> : null}
    </div>
  );
}

export default function AttachmentField({
  config,
  name,
  disabled: forceDisabled,
  formik
}: FormFieldProps) {
  const { dialogBackend, i18n } = useGlobal();
  const [field, meta, { setValue }] = useField(name ?? 'AttachmentField');

  const {
    upload_url,
    item_type,
    fullWidth = true,
    margin = 'normal',
    size,
    max_upload_filesize,
    maxFiles,
    disabled,
    accept
  } = config;

  const [attachments, setAttachments] = useState<AttachmentItemProps['item'][]>(
    field.value || []
  );
  const [hasAttachment, setHasAttachment] = useState<boolean>(
    Array.isArray(attachments) && attachments.length > 0
  );
  const inputRef = useRef<HTMLInputElement>();
  const placeholder = config.placeholder || 'attach_files';

  useEffect(() => {
    setValue(attachments);
    setHasAttachment(attachments.length > 0);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [attachments]);

  useEffect(() => {
    // update state local when clear form field
    if (!isEqual(field.value, attachments)) {
      setAttachments(field.value || []);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [field.value]);

  const handleResetValue = (
    event: React.MouseEvent<HTMLInputElement, MouseEvent>
  ) => {
    event.currentTarget.value = null;
  };

  const handleDelete = useCallback(
    (index: number) => {
      dialogBackend
        .confirm({
          message: i18n.formatMessage({
            id: 'are_you_sure_you_want_to_delete_attachment_file'
          }),
          title: i18n.formatMessage({ id: 'are_you_sure' })
        })
        .then(oke => {
          if (!oke) return;

          setAttachments(prev =>
            produce(prev, draft => {
              if (draft[index].id) {
                draft[index].status = 'remove';
              } else {
                draft.splice(index, 1);
              }
            })
          );
          setHasAttachment(attachments.length > 0);
        });
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  const handleInputChange = useCallback(() => {
    const newAttachments = attachments.filter(
      attachment => attachment.status !== 'remove'
    );

    const file = inputRef.current.files;

    if (!file?.length) return;

    const totalNewFile = newAttachments.length + file.length;

    if (totalNewFile > maxFiles) {
      dialogBackend.alert({
        message: i18n.formatMessage(
          { id: 'maxfile_upload' },
          {
            maxFiles
          }
        )
      });

      return;
    }

    const fileItems: AttachmentItemProps[] = [];

    for (const index of Object.keys(file)) {
      const itemResult: AttachmentItemProps = {
        item: {
          id: 0,
          status: 'create',
          upload_url,
          download_url: URL.createObjectURL(file[index]),
          source: URL.createObjectURL(file[index]),
          file: file[index],
          file_name: file[index].name,
          file_size: file[index].size,
          file_type: file[index].type,
          uid: uniqueId('file'),
          fileItemType: item_type,
          extension: getFileExtension(file[index].name)
        }
      };
      const fileItemSize = itemResult.item.file.size;
      const fileItemName = itemResult.item.file_name;

      if (fileItemSize > max_upload_filesize && max_upload_filesize !== 0) {
        dialogBackend.alert({
          message: i18n.formatMessage(
            { id: 'warning_upload_limit_one_file' },
            {
              fileName: shortenFileName(fileItemName, 30),
              fileSize: parseFileSize(itemResult.item.file.size),
              maxSize: parseFileSize(max_upload_filesize)
            }
          )
        });

        break;
      }

      fileItems.push(itemResult);
    }

    if (fileItems.length) {
      fileItems.forEach(file => {
        setAttachments(prev =>
          produce(prev, draft => {
            draft.push(file.item);
          })
        );
      });

      setHasAttachment(attachments.length > 0);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [attachments]);

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
      <div>
        <AttachmentButton
          variant="outlined"
          size="small"
          color="primary"
          data-testid={camelCase(`button ${name}`)}
          onClick={handleControlClick}
          disabled={disabled || forceDisabled || formik.isSubmitting}
          startIcon={<LineIcon icon="ico-paperclip-alt" />}
        >
          {i18n.formatMessage({ id: placeholder })}
        </AttachmentButton>
      </div>
      {hasAttachment &&
        attachments.map((item, index) => {
          return (
            <AttachmentItem
              key={index.toString()}
              handleDelete={handleDelete}
              index={index}
              item={item}
              error={get(meta.error, index)}
            />
          );
        })}
      <input
        onClick={handleResetValue}
        data-testid={camelCase(`input ${name}`)}
        ref={inputRef}
        style={fixInputStyle}
        accept={accept}
        aria-hidden
        type="file"
        multiple
        onChange={handleInputChange}
        hidden
      />
    </FormControl>
  );
}
