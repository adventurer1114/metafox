/**
 * @type: ui
 * name: StatusComposerAttatchedShareItem
 */

import { StatusComposerControlProps, useGlobal } from '@metafox/framework';
import { get } from 'lodash';
import React from 'react';

export default function StatusComposerAttatchedShareItem({
  composerRef,
  ...props
}: StatusComposerControlProps) {
  const { jsxBackend } = useGlobal();
  const config = get(composerRef.current.state, 'attachments.shareItem.value');

  if (!config) return null;

  const { embedView, identity } = config;
  const EmbedView = jsxBackend.get(embedView);

  if (!EmbedView) return null;

  return (
    <div style={{ padding: 16, position: 'relative' }}>
      <div
        style={{
          position: 'absolute',
          height: '100%',
          width: 'calc(100% - 32px)',
          opacity: 0,
          zIndex: 1
        }}
      ></div>
      <EmbedView identity={identity} {...props} />
    </div>
  );
}
