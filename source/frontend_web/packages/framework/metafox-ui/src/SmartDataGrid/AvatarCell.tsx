/**
 * @type: ui
 * name: dataGrid.cell.AvatarCell
 */

import { UserAvatar } from '@metafox/ui';
import React from 'react';

export default function AvatarCell({ row }) {
  return (
    <div className="middleAlign">
      <UserAvatar
        size={32}
        user={{ ...row, link: row?.user_link || row?.link }}
        srcSizePrefers={'50x50'}
      />
    </div>
  );
}
