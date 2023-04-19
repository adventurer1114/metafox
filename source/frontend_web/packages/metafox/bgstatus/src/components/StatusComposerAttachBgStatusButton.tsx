/**
 * @type: ui
 * name: statusComposer.control.AttachBackgroundStatusButton
 */
import { StatusComposerControlProps } from '@metafox/framework';
import { getImageSrc } from '@metafox/utils';
import React from 'react';
import Base from './AttachBackgroundStatus/AttachBackgroundStatus';

export default function AttachBgStatusToStatusComposerButton({
  composerRef,
  editorRef,
  disabled,
  value,
  ...rest
}: StatusComposerControlProps) {
  const clearBackgroundStatus = () => {
    composerRef.current.removeBackground();
    editorRef.current.focus();
  };

  const selectBackgroundStatus = (item: any) => {
    const className = 'withBackgroundStatus';
    const textAlignment = 'center';
    const editorStyle: React.CSSProperties = {
      fontSize: '28px',
      color: 'white',
      textAlign: 'center',
      backgroundSize: 'cover',
      backgroundImage: `url("${getImageSrc(item.image)}")`,
      minHeight: 371,
      marginTop: 16,
      marginBottom: 16,
      cursor: 'text'
    };

    composerRef.current.setBackground({
      className,
      textAlignment,
      item,
      editorStyle
    });
    setImmediate(() => editorRef.current.focus());
  };

  if (disabled) return null;

  return (
    <Base
      {...rest}
      selectedId={
        value?.status_background_id ||
        composerRef.current.state.attachments.statusBackground?.value?.id
      }
      onClear={clearBackgroundStatus}
      onSelectItem={selectBackgroundStatus}
    />
  );
}
