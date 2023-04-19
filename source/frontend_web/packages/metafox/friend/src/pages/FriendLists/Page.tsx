/**
 * @type: route
 * name: friend.lists
 * path: /friends/lists
 * chunkName: pages.friend
 * bundle: web
 */
import { useGlobal } from '@metafox/framework';
import { Page } from '@metafox/layout';
import * as React from 'react';

export default function BrowseFriendListPage(props) {
  const { createPageParams } = useGlobal();
  const pageParams = createPageParams<{ appName: string; tab: string }>(
    props,
    () => ({
      appName: 'friend',
      tab: 'friend_lists'
    })
  );

  return <Page pageName="friend.lists" pageParams={pageParams} loginRequired />;
}
