/**
 * @type: route
 * name: user.register
 * path: /register
 * bundle: web
 */
import { LOGGED_OUT, useGlobal, useResourceAction } from '@metafox/framework';
import { Page } from '@metafox/layout';
import { APP_USER } from '@metafox/user/constant';
import * as React from 'react';

export default function RegisterPage(props) {
  const {
    i18n,
    createPageParams,
    dispatch,
    createContentParams,
    getSetting,
    redirectTo,
    useSession
  } = useGlobal();
  const canRegister = getSetting('user.allow_user_registration');
  const { loggedIn } = useSession();

  if (!canRegister || loggedIn) {
    redirectTo('/');
  }

  const helmet = {
    title: i18n.formatMessage({ id: 'create_new_account' })
  };

  const config = useResourceAction(APP_USER, APP_USER, 'getRegisterForm');

  dispatch({ type: LOGGED_OUT });

  const pageParams = createPageParams(props, () => ({
    pageMetaName: 'user.user.register',
    shouldShowMenuHeaderLogin: true
  }));

  const contentParams = createContentParams({
    mainForm: {
      noBreadcrumb: true,
      dataSource: {
        apiUrl: config?.apiUrl
      },
      minHeight: '60vh'
    }
  });

  return (
    <Page
      pageName="user.register"
      pageHelmet={helmet}
      contentParams={contentParams}
      pageParams={pageParams}
    />
  );
}
