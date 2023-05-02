/**
 * @type: dialog
 * name: announcement.dialog.listViewer
 */

import { useGlobal } from '@metafox/framework';
import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { ScrollContainer } from '@metafox/layout';
import React from 'react';

export type ListViewDialogProps = {
  apiUrl: string;
  apiParams: Record<string, any>;
  dialogTitle: string;
  pagingId: string;
};

export default function ListViewDialog({
  apiUrl,
  apiParams,
  pagingId,
  dialogTitle
}: ListViewDialogProps) {
  const { useDialog, ListView, i18n } = useGlobal();
  const dataSource = { apiUrl, apiParams };
  const { dialogProps } = useDialog();

  return (
    <Dialog {...dialogProps} maxWidth="xs" fullWidth>
      <DialogTitle>{i18n.formatMessage({ id: dialogTitle })}</DialogTitle>
      <DialogContent variant="fitScroll" sx={{ height: '45vh' }}>
        <ScrollContainer style={{ height: 'auto' }}>
          <ListView
            dataSource={dataSource}
            pagingId={pagingId}
            canLoadMore
            clearDataOnUnMount
            gridLayout="Friend - Small List"
            itemLayout="Friend - Small List"
            itemView="event.itemView.hostSmallCard"
          />
        </ScrollContainer>
      </DialogContent>
    </Dialog>
  );
}
