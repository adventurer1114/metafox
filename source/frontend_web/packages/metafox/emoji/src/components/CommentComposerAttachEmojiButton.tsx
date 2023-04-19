/**
 * @type: ui
 * name: commentComposer.control.attachEmoji
 *
 */

import React from 'react';
import Base from './AttachEmojiButton';
import insertText from './insertText';

function AttachEmojiToStatusComposer(props: any) {
  const onEmojiClick = (text: string) => {
    const { editorState, onChange } = props.editorRef.current.props;
    const nextState = insertText(text, editorState);

    onChange(nextState);
  };

  return <Base size="small" multiple onEmojiClick={onEmojiClick} {...props} />;
}

export default React.memo(AttachEmojiToStatusComposer, () => true);
