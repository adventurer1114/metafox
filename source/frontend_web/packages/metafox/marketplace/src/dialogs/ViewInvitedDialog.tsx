/**
 * @type: dialog
 * name: marketplace.dialog.viewInvitedDialog
 */
import { Dialog, DialogTitle, DialogContent } from '@metafox/dialog';
import { useGlobal } from '@metafox/framework';
import { ScrollProvider } from '@metafox/layout';
import React, { useRef } from 'react';

export default function ViewGuestDialog({ item, dataSource }) {
  const scrollRef = useRef();
  const { useDialog, i18n, ListView } = useGlobal();
  const { dialogProps } = useDialog();

  const { resource_name, item_id } = item;
  const pagingId = `marketplace/${resource_name}/${item_id}`;
  const gridContainerProps = { spacing: 0 };

  return (
    <Dialog {...dialogProps} maxWidth="sm" fullWidth>
      <DialogTitle>{i18n.formatMessage({ id: 'invited_people' })}</DialogTitle>
      <DialogContent>
        <div ref={scrollRef}>
          <ScrollProvider scrollRef={scrollRef}>
            <ListView
              dataSource={dataSource}
              pagingId={pagingId}
              canLoadMore
              clearDataOnUnMount
              gridContainerProps={gridContainerProps}
              gridLayout="Friend - Small List"
              itemLayout="User - List"
              itemView={'marketplace.itemView.peopleCard'}
            />
          </ScrollProvider>
        </div>
      </DialogContent>
    </Dialog>
  );
}
