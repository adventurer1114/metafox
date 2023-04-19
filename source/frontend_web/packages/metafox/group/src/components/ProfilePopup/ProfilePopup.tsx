/* eslint-disable no-prototype-builtins */
import { Link, useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import {
  MEMBERSHIP,
  MEMBERSHIP_CONFIRM_AWAIT,
  NOT_MEMBERSHIP
} from '@metafox/group/constant';
import {
  ButtonList,
  ItemActionMenu,
  LineIcon,
  TruncateText
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Button, Paper, Popper } from '@mui/material';
import * as React from 'react';
import useStyles from './ProfilePopup.styles';

export default function ProfilePopup({
  item,
  actions,
  handleAction,
  state,
  identity,
  open,
  loaded,
  anchorEl
}) {
  const { i18n, useSession, popoverBackend, assetUrl } = useGlobal();
  const { loggedIn } = useSession();
  const classes = useStyles();

  if (!loaded || !loggedIn || !item) return null;

  const to = `/group/${item.id}`;
  const cover = getImageSrc(
    item?.cover,
    '500',
    assetUrl('group.cover_no_image')
  );
  const total_member = item.statistic?.total_member || 0;

  return (
    <Popper
      open={open}
      style={{ zIndex: 1300 }}
      anchorEl={anchorEl}
      onMouseEnter={popoverBackend.onEnterContent}
      onMouseLeave={popoverBackend.onLeaveContent}
    >
      <Paper className={classes.paper}>
        <div
          className={classes.cover}
          style={{ backgroundImage: `url(${cover})` }}
        ></div>
        <div className={classes.popupInner}>
          <div className={classes.userName}>
            <TruncateText lines={2} className={classes.link}>
              <Link color="inherit" to={to}>
                {item.title}
              </Link>
            </TruncateText>
          </div>
          {item.description && (
            <div className={classes.description}>
              <TruncateText lines={3} variant="body1">
                <HtmlViewer html={item.description} />
              </TruncateText>
            </div>
          )}
          <div className={classes.type}>{item.reg_name}</div>
          <div className={classes.statistic}>
            {i18n.formatMessage(
              { id: 'total_member' },
              { value: total_member }
            )}
          </div>
          {loggedIn ? (
            <div className={classes.buttonWrapper}>
              <ButtonList variant="fillFirst">
                {item.membership === NOT_MEMBERSHIP && (
                  <Button
                    variant="contained"
                    size="medium"
                    startIcon={<LineIcon icon={' ico-user-man-three-o'} />}
                    color="primary"
                    onClick={actions.joinGroup}
                  >
                    {i18n.formatMessage({ id: 'join_group' })}
                  </Button>
                )}
                {item.membership === MEMBERSHIP_CONFIRM_AWAIT && (
                  <Button
                    disabled
                    variant="contained"
                    size="medium"
                    startIcon={<LineIcon icon={'ico-clock-o'} />}
                    color="primary"
                  >
                    {i18n.formatMessage({ id: 'request_sent' })}
                  </Button>
                )}
                {item.membership === MEMBERSHIP && (
                  <Button
                    disabled
                    variant="contained"
                    size="medium"
                    startIcon={<LineIcon icon={'ico-check'} />}
                    color="primary"
                  >
                    {i18n.formatMessage({ id: 'joined' })}
                  </Button>
                )}
                <ItemActionMenu
                  identity={identity}
                  state={state}
                  handleAction={handleAction}
                  size="medium"
                  variant="outlined-square"
                  color="primary"
                  icon="ico-dottedmore-o"
                  tooltipTitle={i18n.formatMessage({ id: 'more_options' })}
                  menuName="profilePopoverMenu"
                  zIndex={1300}
                />
              </ButtonList>
            </div>
          ) : null}
        </div>
      </Paper>
    </Popper>
  );
}

ProfilePopup.LoadingSkeleton = () => null;

ProfilePopup.displayName = 'GroupItem_ProfilePopup';
