/**
 * @type: route
 * name: core.admincp.setting.edit
 * path: /admincp/:appName/setting/:resourceName?
 * chunkName: pages.admincp
 * bundle: admincp
 * priority: 9999
 */
import { RemoteDataSource, useGlobal } from '@metafox/framework';
import { Page } from '@metafox/layout';
import { normalizePageName } from '@metafox/utils';
import React from 'react';

type State = {
  readonly appName: string;
  readonly resourceName: string;
  readonly dataSource: RemoteDataSource;
};

export default function AdminEditSetting(props: any) {
  const { createPageParams } = useGlobal();

  const pageParams = createPageParams<State>(
    props,
    ({ appName, resourceName }) => {
      return {
        appName,
        pageMetaName: normalizePageName(
          appName,
          resourceName,
          false,
          false,
          'setting'
        ),
        dataSource: {
          apiUrl: resourceName
            ? `/admincp/setting/form/${appName}/${resourceName}`
            : `/admincp/setting/form/${appName}`
        }
      };
    }
  );

  return <Page pageName={'core.admincp.setting'} pageParams={pageParams} />;
}
