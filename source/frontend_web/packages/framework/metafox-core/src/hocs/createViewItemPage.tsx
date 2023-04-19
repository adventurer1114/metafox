import {
  GET_STATICS,
  PageCreatorConfig,
  PageParams,
  useAbortControl,
  useGlobal
} from '@metafox/framework';
import { Page } from '@metafox/layout';
import { get } from 'lodash';
import React, { createElement, useCallback, useEffect, useState } from 'react';
import { fetchDetail } from '../actions';
import { useResourceAction } from '../hooks';
interface Params extends PageParams {
  resourceName: string;
}

interface Config<T> extends PageCreatorConfig<T> {
  readonly idName?: string;
  readonly resourceName: string;
}

export default function createViewItemPage<T extends Params = Params>({
  appName,
  resourceName,
  pageName,
  idName = 'id',
  loginRequired = false,
  paramCreator
}: Config<T>) {
  function ViewItemDetail(props: any) {
    const { dispatch, createPageParams, jsxBackend } = useGlobal();
    const [loading, setLoading] = useState<boolean>(true);
    const [err, setErr] = useState<number>(0);
    const abortId = useAbortControl();

    const pageParams = createPageParams(
      props,
      (prev: any) => ({
        appName,
        id: prev[idName],
        module_name: appName,
        resource_name: resourceName,
        resourceName,
        item_type: resourceName,
        pageMetaName: `${appName}.${resourceName}.view_detail`,
        identity: `${appName}.entities.${resourceName}.${prev[idName]}`,
        _pageType: 'viewItem'
      }),
      paramCreator
    );

    const config = useResourceAction(appName, resourceName, 'viewItem');

    const onFailure = useCallback((error: any) => {
      // eslint-disable-next-line no-console
      setErr(error);
    }, []);

    const onSuccess = useCallback(() => {
      dispatch({ type: GET_STATICS, payload: { pageParams } });

      setLoading(false);
    }, []);

    useEffect(() => {
      dispatch({ type: `renderPage/${pageName}`, payload: pageParams });
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pageParams]);

    useEffect(() => {
      dispatch(
        fetchDetail(
          config.apiUrl,
          { apiParams: config.apiParams, pageParams },
          onSuccess,
          onFailure,
          abortId
        )
      );

      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pageParams.identity, pageParams]);

    if (err) {
      const message =
        get(err, 'response.data.error') || get(err, 'response.data.message');

      const pageName =
        get(err, 'response.status') === 403 ? 'core.error403' : 'core.error404';

      return (
        <Page
          pageName={pageName}
          loginRequired={loginRequired}
          pageParams={{ title: message, variant: 'h2' }}
        />
      );
    }

    if (loading) return jsxBackend.render({ component: 'Loading' });

    return createElement(Page, {
      pageName,
      pageParams,
      loginRequired
    });
  }

  ViewItemDetail.displayName = `ViewItemDetail(${pageName})`;

  return ViewItemDetail;
}
