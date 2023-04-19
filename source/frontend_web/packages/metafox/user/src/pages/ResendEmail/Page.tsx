/**
 * @type: route
 * name: user.resend_email
 * path: /resend-email
 * bundle: web
 */

import { useGlobal } from '@metafox/framework';
import { Page } from '@metafox/layout';
import React from 'react';

const SendEmailPage = props => {
  const { createPageParams } = useGlobal();

  const pageParams = createPageParams<{ name: string }>(props, () => ({
    pageMetaName: 'user.user.verify_email',
    shouldShowMenuHeaderLogin: true
  }));

  return <Page pageName="user.resend_email" pageParams={pageParams} />;
};

export default SendEmailPage;
