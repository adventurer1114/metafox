/**
 * @type: dialog
 * name: group.manager.dialog.membershipQuestion
 */

import { Dialog, useDialog } from '@metafox/dialog';
import React from 'react';
import { RemoteFormBuilder } from '@metafox/form';

const GroupMembershipQuestionDialog = ({
  id,
  dataSource
}: {
  id: string;
  dataSource: any;
}) => {
  const { dialogProps } = useDialog();

  return (
    <Dialog fullWidth maxWidth="xs" {...dialogProps}>
      <RemoteFormBuilder dialog dataSource={dataSource} />
    </Dialog>
  );
};

export default GroupMembershipQuestionDialog;
