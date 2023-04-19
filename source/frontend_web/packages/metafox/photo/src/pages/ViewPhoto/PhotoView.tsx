/**
 * @type: route
 * name: photo.view
 * path: /photo/:photo_id(\d+), /photo/:photo_set(\d+)/:photo_id(\d+), /media/:photo_set(\d+)/photo/:photo_id(\d+), /media/album/:photo_album(\d+)/photo/:photo_id(\d+)
 * chunkName: pages.photo
 * bundle: web
 */
import { fetchDetail, useGlobal, useResourceAction } from '@metafox/framework';
import { Page } from '@metafox/layout';
import { APP_PHOTO, RESOURCE_PHOTO } from '@metafox/photo/constant';
import { get } from 'lodash';
import * as React from 'react';
import { useLocation, useNavigate } from 'react-router';

export default function HomePage(props) {
  const {
    createPageParams,
    createContentParams,
    useGetItem,
    dispatch,
    jsxBackend
  } = useGlobal();

  const config = useResourceAction(APP_PHOTO, RESOURCE_PHOTO, 'viewAll');
  const navigate = useNavigate();
  const location = useLocation();

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
  const pageParams = createPageParams<{
    appName: string;
    resourceName: string;
    photo_id: string | number;
  }>(props, prev => ({
    appName: APP_PHOTO,
    resourceName: RESOURCE_PHOTO,
    tab: 'landing',
    pageMetaName: `${APP_PHOTO}.${RESOURCE_PHOTO}.landing`,
    _pageType: 'browseItem'
  }));
  const item = useGetItem(
    `${pageParams?.appName}.entities.${pageParams?.resourceName}.${pageParams?.photo_id}`
  );

  const itemAlbum = useGetItem(item?.album);

  React.useEffect(() => {
    if (pageParams?.photo_id && loading) {
      // dispatch here on check error page
      dispatch(
        fetchDetail(
          '/photo/:id',
          { id: pageParams?.photo_id },
          onSuccess,
          onFailure
        )
      );
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const contentParams = createContentParams({
    mainListing: {
      canLoadMore: true,
      contentType: RESOURCE_PHOTO,
      title: pageParams?.heading,
      dataSource: {
        apiUrl: config?.apiUrl,
        apiRules: config?.apiRules,
        apiParams: { ...config?.apiParams, ...pageParams }
      }
    }
  });

  React.useEffect(() => {
    if (loading && item && !err) {
      const to = { pathname: location.pathname };
      const state = {
        asModal: true,
        loadedDetail: true,
        modalCloseTo: itemAlbum
          ? { pathname: `/photo/album/${itemAlbum.id}` }
          : { pathname: '/photo' }
      };

      setImmediate(() => {
        navigate(to, { state, replace: true });
      });
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
      pageName={itemAlbum ? 'photo_album.view' : 'photo.home'}
      pageParams={pageParams}
      contentParams={contentParams}
    />
  );
}
