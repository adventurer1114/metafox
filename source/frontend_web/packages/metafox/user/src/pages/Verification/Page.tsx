/**
 * @type: route
 * name: user.email_verification
 * path: /user/email-verification/:hash
 * bundle: web
 */

import { useGlobal } from '@metafox/framework';
import { Page } from '@metafox/layout';
import React from 'react';

// support pageParams to control returnUrl
const EmailVerificationPage = props => {
  const { createPageParams } = useGlobal();

  const pageParams = createPageParams<{ name: string; hash: string }>(
    props,
    () => ({
      pageMetaName: 'user.user.verify_email'
    })
  );

  return <Page pageName="user.email_verification" pageParams={pageParams} />;
};

export default EmailVerificationPage;
