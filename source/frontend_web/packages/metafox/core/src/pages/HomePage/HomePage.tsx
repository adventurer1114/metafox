/**
 * @type: route
 * name: core.home
 * path: /, /home
 * chunkName: boot
 * bundle: web
 */
import { APP_FEED } from '@metafox/feed';
import { useGlobal } from '@metafox/framework';
import { Page } from '@metafox/layout';
import * as React from 'react';

export default function HomePage() {
  const { createPageParams } = useGlobal();

  const pageParams = createPageParams({}, () => ({
    module_name: APP_FEED,
    item_type: APP_FEED,
    appName: APP_FEED,
    pageMetaName: `activity.${APP_FEED}.home`
  }));

  return <Page pageName="home.member" pageParams={pageParams} loginRequired />;
}
