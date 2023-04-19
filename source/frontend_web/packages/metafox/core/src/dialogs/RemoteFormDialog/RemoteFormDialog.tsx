/**
 * @type: dialog
 * name: core.dialog.RemoteForm
 */
import { useGlobal } from '@metafox/framework';
import { Dialog } from '@metafox/dialog';
import { RemoteFormBuilderProps, SmartFormBuilder } from '@metafox/form';
import { DialogProps } from '@mui/material';
import React from 'react';

type Props = {
  title?: string;
  maxWidth?: DialogProps['maxWidth'];
} & RemoteFormBuilderProps;

export default function RemoteForm({
  title = 'Edit',
  maxWidth = 'sm',
  ...rest
}: Props) {
  const { useDialog } = useGlobal();
  const dialogItem = useDialog();
  const { dialogProps } = dialogItem;

  return (
    <Dialog
      {...dialogProps}
      maxWidth={maxWidth}
      fullWidth
      data-testid="editPopup"
    >
      <SmartFormBuilder
        dialog
        dialogTitle={title}
        dialogItem={dialogItem}
        {...rest}
      />
    </Dialog>
  );
}
