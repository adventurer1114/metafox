import {
  AppResourceAction,
  getDeepItem,
  GlobalState,
  PageCreatorConfig,
  PageParams,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import { Page } from '@metafox/layout';
import produce from 'immer';
import { get } from 'lodash';
import React, { createElement, useEffect } from 'react';
import { useSelector } from 'react-redux';
import { fetchDetail } from '../actions';

interface Params extends PageParams {
  resourceName: string;
}

interface Config<T> extends PageCreatorConfig<T> {
  readonly idName?: string;
  readonly resourceName: string;
}

export default function createProfilePage<T extends Params = Params>({
  appName,
  resourceName,
  pageName,
  idName = 'id',
  loginRequired = false,
  paramCreator
}: Config<T>) {
  function ProfilePage(props: any) {
    const { dispatch, createPageParams, jsxBackend } = useGlobal();

    const [err, setErr] = React.useState<number>(0);
    const [loading, setLoading] = React.useState(true);

    const pageParams = createPageParams(
      props,
      prev => ({
        appName,
        profile_page: true,
        tab: prev.tab,
        module_name: appName,
        resource_name: resourceName,
        resourceName,
        profile_id: prev[idName],
        profile_type: resourceName,
        item_type: resourceName,
        item_id: prev[idName],
        identity: `${appName}.entities.${resourceName}.${prev[idName]}`,
        _pageType: 'profile'
      }),
      ({ tab }) => ({
        pageMetaName: `${appName}.profile.${tab ?? 'home'}`
      }),
      paramCreator
    );

    const item = useSelector<GlobalState, T>(state =>
      getDeepItem(state, pageParams?.identity)
    );

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const config: AppResourceAction = useResourceAction(
      appName,
      resourceName,
      'viewItem'
    );

    useEffect(() => {
      dispatch({ type: `renderPage/${pageName}`, payload: pageParams });
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pageParams]);

    useEffect(() => {
      if (!item) return;

      setLoading(false);
    }, [item]);

    // how to handle error
    // 404 Page
    // 403 Page
    // 503 Page
    // 500 Page

    React.useEffect(() => {
      if (!pageParams[idName]) return;

      dispatch(
        fetchDetail(
          config.apiUrl,
          {
            pageParams,
            apiParams: config.apiParams,
            id: pageParams[idName]
          },
          undefined,
          setErr,
          undefined
        )
      );

      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pageParams[idName]]);

    if (err) {
      const message =
        get(err, 'response.data.error') || get(err, 'response.data.message');

      const pageName =
        get(err, 'response.status') === 403 ? 'core.error403' : 'core.error404';

      return (
        <Page
          pageName={pageName}
          pageParams={{ title: message, variant: 'h2' }}
        />
      );
    }

    if (loading) return jsxBackend.render({ component: 'Loading' });

    if (!item?._loadedDetail) return null;

    const modifiedPageParams = produce(pageParams, draft => {
      draft.tab = pageParams.tab ?? item?.defaultActiveTabMenu ?? 'home';
    });

    return createElement(Page, {
      pageName: `${pageName}.${modifiedPageParams.tab}`,
      loginRequired,
      pageParams: modifiedPageParams
    });
  }

  ProfilePage.displayName = `ProfilePage(${pageName})`;

  return ProfilePage;
}
