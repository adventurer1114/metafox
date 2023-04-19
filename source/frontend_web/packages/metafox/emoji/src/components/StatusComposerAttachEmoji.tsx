/**
 * @type: ui
 * name: statusComposer.control.AttachEmojiButton
 */

import { LineIcon } from '@metafox/ui';
import { IconButton, Tooltip } from '@mui/material';
import React from 'react';
import Base from './AttachEmojiButton';
import insertText from './insertText';

const DefaultControl = React.forwardRef(
  ({ onClick, title, icon }: any, ref: any) => {
    return (
      <Tooltip title={title}>
        <IconButton
          onClick={onClick}
          size="small"
          ref={ref}
          role="button"
          sx={{
            p: 0.5,
            color: theme => {
              return theme.palette.mode === 'light'
                ? 'rgba(0, 0, 0, 0.54)'
                : '#fff';
            }
          }}
        >
          <LineIcon icon={icon} />
        </IconButton>
      </Tooltip>
    );
  }
);

function AttachEmojiToStatusComposer(props: any) {
  const onEmojiClick = (text: string) => {
    const { editorState, onChange } = props.editorRef.current.props;
    const nextState = insertText(text, editorState);

    onChange(nextState);
  };

  return (
    <Base
      size="medium"
      multiple
      control={DefaultControl}
      onEmojiClick={onEmojiClick}
      buttonStyle={{ padding: 4 }}
      {...props}
    />
  );
}

export default React.memo(AttachEmojiToStatusComposer, () => true);
