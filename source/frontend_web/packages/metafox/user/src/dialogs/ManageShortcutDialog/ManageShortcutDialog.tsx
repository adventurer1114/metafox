/**
 * @type: dialog
 * name: user.dialog.ManageShortcutDialog
 */
import { useGlobal } from '@metafox/framework';
import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { ScrollContainer } from '@metafox/layout';
import { DialogSearchInput } from '@metafox/ui';
import { Box } from '@mui/material';
import React from 'react';

export default function ManageShortcutDialog() {
  const { useDialog, i18n, ListView } = useGlobal();
  const { dialogProps } = useDialog();
  const [query, setQuery] = React.useState<string>('');

  const handleChanged = React.useCallback((value: string) => {
    setQuery(value);
  }, []);

  const dialogTitle = i18n.formatMessage({ id: 'edit_your_shortcuts' });

  const pagingId = React.useMemo(() => {
    return `/user/shortcut/edit/?query=${query}`;
  }, [query]);

  React.useEffect(() => {
    return () => {
      // should reload shortcut on home page.
    };
  }, []);

  return (
    <Dialog {...dialogProps} maxWidth="xs" fullWidth>
      <DialogTitle>{dialogTitle}</DialogTitle>
      <DialogContent variant="fitScroll" sx={{ height: '45vh' }}>
        <Box sx={{ p: 2 }}>
          <DialogSearchInput
            placeholder={i18n.formatMessage({
              id: 'search_your_page_groups_dots'
            })}
            onChanged={handleChanged}
          />
        </Box>
        <ScrollContainer autoHeightMax={'100%'} autoHide autoHeight>
          <ListView
            emptyPage="core.block.no_content"
            emptyPageProps={{
              title: 'no_results_try_another_search'
            }}
            dataSource={{
              apiUrl: '/user/shortcut/edit',
              apiParams: { q: query || undefined }
            }}
            pagingId={pagingId}
            canLoadMore
            clearDataOnUnMount
            itemView="shortcut.itemView.mainCard"
            gridLayout="Shortcut - Small Lists"
            itemLayout="Shortcut - Small Lists"
          />
        </ScrollContainer>
      </DialogContent>
    </Dialog>
  );
}
