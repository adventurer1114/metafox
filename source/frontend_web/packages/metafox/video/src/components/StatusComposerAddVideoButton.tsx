/**
 * @type: ui
 * name: statusComposer.control.StatusUploadVideoButton
 */
import { StatusComposerControlProps, useGlobal } from '@metafox/framework';
import React from 'react';
import useAddVideoToStatusComposerHandler from '../hooks/useAddVideoToStatusComposerHandler';

export default function StatusUploadVideoButton(
  props: StatusComposerControlProps
) {
  const { disabled, control: Control } = props;
  const { i18n } = useGlobal();
  const accept = 'video/*';
  const multiple = false;
  const inputRef = React.useRef<HTMLInputElement>();
  const [handleChange, onClick] = useAddVideoToStatusComposerHandler(
    props.composerRef,
    inputRef
  );

  return (
    <>
      <Control
        onClick={onClick}
        disabled={disabled}
        testid="buttonAttachVideo"
        icon="ico-video"
        label={i18n.formatMessage({ id: 'video' })}
        title={i18n.formatMessage({
          id: disabled ? 'this_cant_be_combined' : 'upload_video'
        })}
      />
      <input
        data-testid="inputAttachVideo"
        onChange={handleChange}
        multiple={multiple}
        ref={inputRef}
        className="srOnly"
        type="file"
        accept={accept}
      />
    </>
  );
}
