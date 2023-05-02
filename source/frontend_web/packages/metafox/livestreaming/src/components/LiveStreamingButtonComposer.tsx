/**
 * @type: ui
 * name: statusComposer.control.LiveStreamingButton
 */
import { StatusComposerControlProps, useGlobal } from '@metafox/framework';
import React from 'react';

export default function LiveStreamingButtonComposer(
  props: StatusComposerControlProps & {
    label: string;
  }
) {
  const { i18n } = useGlobal();
  const { control: Control, disabled, label } = props;

  return (
    <Control
      disabled={disabled}
      testid="livestreamingCreate"
      href={'/live-video/add'}
      icon="ico-videocam-o"
      label={label}
      title={i18n.formatMessage({
        id: 'create_live_video'
      })}
    />
  );
}
