/**
 * @type: modalRoute
 * name: media.viewModal
 * path: /media/:photo_set(\d+)/:media_type/:media_id(\d+), /media/album/:photo_album(\d+)/:media_type/:media_id(\d+)
 * bundle: web
 */
import { fetchDetail, useGlobal } from '@metafox/framework';
import * as React from 'react';

export default function PhotoViewModal(props) {
  const { dispatch, createPageParams, dialogBackend, use } = useGlobal();
  const pageParams = createPageParams<{
    photo_set: string;
    photo_album: string;
    media_id: string;
    media_type?: string;
  }>(props, () => ({
    _pageType: 'viewItemInModal'
  }));
  const [loading, setLoading] = React.useState(false);
  const { media_id, media_type, photo_set, photo_album } = pageParams;
  const identity = `${media_type}.entities.${media_type}.${media_id}`;

  const fetchSuccess = () => {
    setLoading(false);
  };

  const fetchError = () => {
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

    if (photo_album) {
      dispatch({
        type: 'photo/photo_album/LOAD',
        payload: {
          photo_album
        }
      });
    }

    use({ getPageParams: () => pageParams });
    const loadedDetail = props?.location?.state?.loadedDetail;

    if (media_id && !loading && !loadedDetail) {
      setLoading(true);
      dispatch(
        fetchDetail(
          `/${media_type}/:id`,
          { id: media_id },
          fetchSuccess,
          fetchError
        )
      );
    }

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [identity, pageParams]);

  React.useEffect(() => {
    dialogBackend.present({
      component: 'media.dialog.mediaView',
      props: {
        identity,
        photo_set,
        photo_album,
        media_id,
        media_type,
        loading
      },
      dialogId: 'viewMedia'
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [identity, pageParams, loading]);

  return null;
}
