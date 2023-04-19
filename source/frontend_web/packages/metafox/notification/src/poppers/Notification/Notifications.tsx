/**
 * @type: ui
 * name: notification.ui.notificationPopper
 */
import { RefOf, useGlobal } from '@metafox/framework';
import { ScrollContainer } from '@metafox/layout';
import { Box, Paper, Popper, PopperProps, Typography } from '@mui/material';
import React from 'react';

export const NotificationPopperContext = React.createContext(null);

export default function NotificationPopper({
  anchorRef,
  open,
  closePopover,
  ...rest
}: PopperProps & {
  anchorRef: RefOf<HTMLDivElement>;
  closePopover: () => void;
}) {
  const { ListView, i18n, dispatch, ItemActionMenu, useActionControl } =
    useGlobal();
  const [handleAction, state] = useActionControl(null, {});
  const pagingId = 'notifications';
  const dataSource = { apiUrl: '/notification', apiParams: { limit: 10 } };

  React.useEffect(() => {
    if (open) {
      dispatch({
        type: 'core/status/clearNotification'
      });
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [open]);

  return (
    <NotificationPopperContext.Provider value={{ closePopover }}>
      <Popper
        id="notification"
        data-testid="notifications"
        anchorEl={anchorRef.current}
        open={open}
        {...rest}
      >
        <Paper
          sx={{
            width: 360,
            overflow: 'hidden',
            userSelect: 'none'
          }}
        >
          <Box
            sx={{
              p: [1.5, 2],
              display: 'flex',
              alignItems: 'center',
              justifyContent: 'space-between'
            }}
          >
            <Typography variant="h4" sx={{ flex: 1 }}>
              {i18n.formatMessage({ id: 'notifications' })}
            </Typography>
            <ItemActionMenu
              appName="notification"
              menuName="notificationSettingsMenu"
              handleAction={handleAction}
              state={state}
              disablePortal
            />
          </Box>
          <ScrollContainer
            autoHide
            autoHeight
            autoHeightMax={400}
            autoHeightMin={40}
          >
            <ListView
              canLoadMore
              dataSource={dataSource}
              pagingId={pagingId}
              clearDataOnUnMount
              itemView={'notification.itemView.smallCard'}
              gridLayout="Notification - Small Lists"
              itemLayout="Notification - Small Lists"
              emptyPage="core.block.no_content"
              emptyPageProps={{
                title: i18n.formatMessage({ id: 'no_notifications' })
              }}
            />
          </ScrollContainer>
        </Paper>
      </Popper>
    </NotificationPopperContext.Provider>
  );
}
