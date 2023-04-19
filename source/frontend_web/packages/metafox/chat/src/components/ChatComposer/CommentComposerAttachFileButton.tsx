/**
 * @type: ui
 * name: commentComposer.control.attachFile
 */

import { useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { styled, Tooltip } from '@mui/material';
import React from 'react';

const WrapperButtonIcon = styled('div')(({ theme }) => ({
  fontSize: theme.spacing(1.875),
  padding: 0,
  display: 'inline-flex',
  alignItems: 'center',
  justifyContent: 'center',
  width: '28px',
  height: '28px',
  minWidth: '28px',
  color:
    theme.palette.mode === 'light'
      ? theme.palette.grey['700']
      : theme.palette.text.primary,
  cursor: 'pointer',
  '&:hover': {
    backgroundColor: theme.palette.action.hover,
    borderRadius: 50
  }
}));

function AttachEmojiToStatusComposer({
  rid,
  onAttachFiles,
  previewRef,
  filesUploadRef,
  control: Control
}: any) {
  const { i18n } = useGlobal();
  const inputRef = React.useRef<HTMLInputElement>();

  const onChangeImage = () => {
    if (!inputRef.current.files.length) return;

    if (previewRef) {
      previewRef.current?.attachFiles(inputRef.current.files);
      previewRef.current?.typeUpload('image');
    }

    if (filesUploadRef) {
      filesUploadRef.current?.attachFiles(inputRef.current.files);
    }

    if (onAttachFiles) onAttachFiles(inputRef.current.files);
  };

  const attachImages = () => {
    inputRef.current.click();
  };

  return (
    <div>
      <Tooltip title={i18n.formatMessage({ id: 'send_photo' })} placement="top">
        <WrapperButtonIcon onClick={attachImages}>
          <LineIcon icon="ico-photo-o" />
        </WrapperButtonIcon>
      </Tooltip>
      <input
        data-testid="inputAttachPhoto"
        onChange={onChangeImage}
        multiple
        ref={inputRef}
        style={{ display: 'none' }}
        type="file"
        accept="image/*"
      />
    </div>
  );
}

export default React.memo(
  AttachEmojiToStatusComposer,
  (prev, next) => prev.rid === next.rid
);
