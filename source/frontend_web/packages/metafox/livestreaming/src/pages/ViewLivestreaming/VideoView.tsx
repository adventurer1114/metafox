/**
 * @type: route
 * name: livestreaming.view
 * path: /live-video/:id(\d+)
 * chunkName: pages.livestreaming
 * bundle: web
 */

import {
  fetchDetail,
  useGlobal,
  useLocation,
  useResourceAction
} from '@metafox/framework';
import { Page } from '@metafox/layout';
import {
  APP_LIVESTREAM,
  RESOURCE_LIVE_VIDEO
} from '@metafox/livestreaming/constants';
import { get } from 'lodash';
import React from 'react';
import { useNavigate } from 'react-router';

export default function HomePage(props) {
  const {
    createPageParams,
    createContentParams,
    useGetItem,
    dispatch,
    jsxBackend
  } = useGlobal();
  const [err, setErr] = React.useState<number>(0);
  const [loading, setLoading] = React.useState(true);
  const onFailure = React.useCallback((error: any) => {
    // eslint-disable-next-line no-console
    setErr(error);
    setLoading(false);
  }, []);
  const onSuccess = React.useCallback(() => {
    setLoading(false);
  }, []);
  const config = useResourceAction(
    APP_LIVESTREAM,
    RESOURCE_LIVE_VIDEO,
    'viewAll'
  );
  const location = useLocation();
  const pageAsModal = location?.state?.asModal;

  const navigate = useNavigate();

  const pageParams = createPageParams<{
    appName: string;
    resourceName: string;
    id: string | number;
  }>(props, prev => ({
    appName: APP_LIVESTREAM,
    resourceName: RESOURCE_LIVE_VIDEO,
    tab: 'landing',
    pageMetaName: `${APP_LIVESTREAM}.${RESOURCE_LIVE_VIDEO}.landing`,
    _pageType: 'browseItem'
  }));

  const contentParams = createContentParams({
    mainListing: {
      canLoadMore: true,
      contentType: RESOURCE_LIVE_VIDEO,
      title: pageParams?.heading,
      dataSource: {
        apiUrl: config?.apiUrl,
        apiRules: config?.apiRules,
        apiParams: { ...config?.apiParams, ...pageParams }
      }
    }
  });
  const item = useGetItem(
    `${pageParams?.appName}.entities.${pageParams?.resourceName}.${pageParams?.id}`
  );

  React.useEffect(() => {
    if (pageParams?.id && !pageAsModal) {
      // dispatch here on check error page
      dispatch(
        fetchDetail(
          '/live-video/:id',
          { id: pageParams?.id },
          onSuccess,
          onFailure
        )
      );
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  React.useEffect(() => {
    if (loading && item && !err) {
      const to = { pathname: location.pathname };
      const state = {
        asModal: true,
        loadedDetail: true,
        modalCloseTo: { pathname: '/live-video' }
      };
      setImmediate(() => navigate(to, { state, replace: true }));
    }

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [item, loading, err]);

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

  return (
    <Page
      pageName={'livestreaming.home'}
      pageParams={pageParams}
      contentParams={contentParams}
    />
  );
}
