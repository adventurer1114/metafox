/**
 * @type: ui
 * name: core.block.no_content_with_icon
 * title: No content with icon
 * keywords: no content
 */

import { getAppMenuSelector, GlobalState, useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { Theme, Button } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import * as React from 'react';
import { useSelector } from 'react-redux';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      root: {
        display: 'flex',
        flexDirection: 'column',
        justifyContent: 'center',
        padding: theme.spacing(0, 2),
        alignItems: 'center',
        marginTop: theme.spacing(11.25)
      },
      icon: {
        fontSize: theme.mixins.pxToRem(72),
        color: theme.palette.text.secondary,
        marginBottom: theme.spacing(4)
      },
      title: {
        fontSize: theme.mixins.pxToRem(24),
        fontWeight: theme.typography.fontWeightBold,
        marginBottom: theme.spacing(1.5),
        textAlign: 'center',
        [theme.breakpoints.down('xs')]: {
          fontSize: theme.mixins.pxToRem(18)
        }
      },
      content: {
        fontSize: theme.mixins.pxToRem(18),
        color: theme.palette.text.secondary,
        textAlign: 'center',
        [theme.breakpoints.down('xs')]: {
          fontSize: theme.mixins.pxToRem(15)
        }
      }
    }),
  {
    name: 'NoContentWithIcon'
  }
);

interface NoContentWithIconProps {
  image: string;
  title: string;
  description?: string;
  labelButton?: string;
  identity?: string;
  action?: string;
}

export default function NoContentWithIcon({
  image: iconProp,
  title: titleProp,
  description,
  labelButton,
  prev_identity,
  action
}: NoContentWithIconProps) {
  const classes = useStyles();
  const { i18n, usePageParams, dispatch } = useGlobal();

  const pageParams = usePageParams();

  const menu = useSelector((state: GlobalState) =>
    getAppMenuSelector(state, pageParams.appName, 'sidebarMenu')
  );

  const identity =
    pageParams?.heading?.props?.identity || `${prev_identity}${pageParams?.id}`;

  const icon =
    iconProp ||
    menu.items.find(item => item.tab === pageParams?.tab)?.icon ||
    'ico-user-circle-o';
  const moduleName = pageParams.appName || pageParams.resourceName;
  const title =
    titleProp ||
    `no${pageParams?.tab ? `_${pageParams?.tab}` : ''}_${moduleName}_found`;

  const onAddNewItem = () => {
    dispatch({ type: action, payload: { identity } });
  };

  return (
    <div className={classes.root}>
      <LineIcon className={classes.icon} icon={icon} />
      <div className={classes.title}>
        <span>{i18n.formatMessage({ id: title })}</span>
      </div>
      {description ? (
        <div className={classes.content}>
          {i18n.formatMessage({ id: description })}
        </div>
      ) : null}
      {labelButton && (
        <Button
          variant="contained"
          color="primary"
          startIcon={<LineIcon icon="ico-plus" />}
          sx={{ fontSize: 18, mt: 2.5 }}
          onClick={onAddNewItem}
        >
          {i18n.formatMessage({ id: labelButton })}
        </Button>
      )}
    </div>
  );
}
