/**
 * @type: route
 * name: user.mfa
 * path: /authenticator
 * bundle: web
 */

import { useGlobal } from '@metafox/framework';
import { Page } from '@metafox/layout';
import React from 'react';

const MFAPage = props => {
  const { createPageParams } = useGlobal();

  const pageParams = createPageParams<{ name: string }>(props, () => ({
    pageMetaName: 'user.user.mfa',
    shouldShowMenuHeaderLogin: true
  }));

  return <Page pageName="user.mfa" pageParams={pageParams} />;
};

export default MFAPage;
