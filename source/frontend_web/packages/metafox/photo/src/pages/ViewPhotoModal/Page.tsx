/**
 * @type: modalRoute
 * name: photo.viewModal
 * path: /photo/:photo_id(\d+), /photo/:photo_set(\d+)/:photo_id(\d+)
 * bundle: web
 */
import { fetchDetail, useGlobal } from '@metafox/framework';
import * as React from 'react';

export default function PhotoViewModal(props) {
  const { dispatch, createPageParams, dialogBackend, use } = useGlobal();
  const [loading, setLoading] = React.useState(true);
  const [error, setErr] = React.useState<number>(0);
  const pageParams = createPageParams<{
    photo_set?: string;
    photo_id: string;
  }>(props, () => ({
    _pageType: 'viewItemInModal'
  }));
  const identity = `photo.entities.photo.${pageParams.photo_id}`;
  const { photo_set, photo_id } = pageParams;

  const fetchSuccess = () => {
    setLoading(false);
  };

  const fetchError = error => {
    setErr(error);
    setLoading(false);
  };

  React.useEffect(() => {
    if (photo_set) {
      dispatch({
        type: 'photo/photo_set/LOAD',
        payload: {
          photo_set
        }
      });
    }

    use({ getPageParams: () => pageParams });
    const loadedDetail = props?.location?.state?.loadedDetail;

    if (photo_id && !loadedDetail) {
      dispatch(
        fetchDetail('/photo/:id', { id: photo_id }, fetchSuccess, fetchError)
      );
    }

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [identity, pageParams]);

  React.useEffect(() => {
    dialogBackend.present({
      component: 'photo.dialog.photoView',
      props: { identity, photo_set, photo_id, loading, error },
      dialogId: 'viewPhoto'
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [identity, pageParams, loading, error]);

  return null;
}
