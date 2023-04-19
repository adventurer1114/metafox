/**
 * @type: dialog
 * name: saved.dialog.friendList
 */

import { useGlobal } from '@metafox/framework';
import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { ScrollContainer } from '@metafox/layout';
import React from 'react';

export type UserFriendsDialogProps = {
  apiUrl: string;
  apiParams: Record<string, any>;
  dialogTitle: string;
  pagingId: string;
};

export default function ListFriendsDialog({
  apiUrl,
  apiParams,
  pagingId,
  dialogTitle
}: UserFriendsDialogProps) {
  const { useDialog, ListView, i18n } = useGlobal();
  const dataSource = { apiUrl, apiParams };
  const { dialogProps } = useDialog();

  return (
    <Dialog {...dialogProps} maxWidth="sm" fullWidth>
      <DialogTitle>{i18n.formatMessage({ id: dialogTitle })}</DialogTitle>
      <DialogContent variant="fitScroll" sx={{ height: '45vh' }}>
        <ScrollContainer style={{ height: 'auto' }}>
          <ListView
            dataSource={dataSource}
            canLoadMore={false}
            clearDataOnUnMount
            gridLayout="Friend - Small List"
            itemLayout="Friend - Small List"
            itemView="saved.itemView.friendList"
            emptyPage="core.block.no_item_with_icon"
            emptyPageProps={{
              description: 'no_friends_found',
              image: 'ico-user-circle-o'
            }}
          />
        </ScrollContainer>
      </DialogContent>
    </Dialog>
  );
}
