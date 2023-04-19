/**
 * @type: siteDock
 * name: GDPR
 */

import { Link, useGlobal } from '@metafox/framework';
import { Button } from '@mui/material';
import React from 'react';
import useStyles from './styles';

export default function GDPR() {
  const classes = useStyles();
  const { cookieBackend, i18n, getSetting } = useGlobal();
  const [userConfirm, setUserConfirm] = React.useState<boolean>(false);
  const enabled = cookieBackend.get('gdpr');

  const onUserConfirm = () => {
    cookieBackend.set('gdpr', 1);
    setUserConfirm(true);
  };

  // dont show gdpr
  if (process.env.MFOX_BUILD_TYPE === 'admincp') return null;

  if (!getSetting('core.general.gdpr_enabled')) return null;

  if (enabled || userConfirm) return null;

  return (
    <div className={classes.root}>
      <div className={classes.contentOuter}>
        <div className={classes.contentInner}>
          <h2 className={classes.title}>
            {i18n.formatMessage({ id: 'cookie_gdpr_title' })}
          </h2>
          <div className={classes.description}>
            {i18n.formatMessage({ id: 'cookie_gdpr_message' })}
            {' '}
            <Link to={'/policy'} color='primary'>
              {i18n.formatMessage({ id: 'cookie_policy' })}
            </Link>
          </div>
        </div>
        <Button
          className={classes.btnConfirm}
          variant="outlined"
          size="medium"
          onClick={onUserConfirm}
        >
          {i18n.formatMessage({ id: 'accept' })}
        </Button>
      </div>
    </div>
  );
}
