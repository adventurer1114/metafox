import { RemoteFormBuilder } from '@metafox/form';
import { useGlobal, useResourceAction } from '@metafox/framework';
import { APP_PHOTO, RESOURCE_ALBUM } from '@metafox/photo/constant';
import { compactUrl } from '@metafox/utils';
import { Alert, Box, CircularProgress, styled } from '@mui/material';
import React from 'react';

const LoadingStyled = styled(Box, {
  shouldForwardProp: prop => prop !== 'size' && prop !== 'isProfilePage'
})<{ size: string; isProfilePage?: boolean }>(
  ({ theme, size, isProfilePage }) => ({
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    ...(size === 'large' && {
      width: isProfilePage ? '100%' : 212,
      height: isProfilePage ? 170 : 125
    }),
    ...(size === 'small' && {
      width: 190,
      height: 40
    })
  })
);
const AlertStyled = styled(Alert, {
  shouldForwardProp: prop => prop !== 'size' && prop !== 'isProfilePage'
})<{ size: string; isProfilePage?: boolean }>(
  ({ theme, size, isProfilePage }) => ({
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center',
    ...(size === 'large' && {
      width: 212,
      height: 125
    }),
    ...(size === 'small' && {
      width: 190,
      height: 40
    }),
    ...(isProfilePage && {
      width: '100%',
      height: 170
    })
  })
);

const AddPhotoAlbumDetail = ({ size = 'small' }) => {
  const { i18n, usePageParams } = useGlobal();
  const pageParams = usePageParams();
  const [albumId, setAlbumId] = React.useState(null);

  React.useEffect(() => {
    if (
      'user' === pageParams?.resource_name &&
      pageParams?.profile_page &&
      pageParams?.album_id
    ) {
      setAlbumId(pageParams.album_id);

      return;
    }

    setAlbumId(pageParams.id);
  }, [pageParams]);

  const resourceAction = useResourceAction(
    APP_PHOTO,
    RESOURCE_ALBUM,
    'addPhotos'
  );
  const { apiUrl, apiMethod } = resourceAction || {};

  const dataSource = {
    apiUrl,
    apiMethod
  };

  const formAddPhotoAlbum = (
    <RemoteFormBuilder
      dataSource={{ apiUrl: compactUrl(dataSource?.apiUrl, { id: albumId }) }}
      loadingComponent={
        <LoadingStyled
          isProfilePage={albumId}
          size={size}
          data-testid="loadingIndicator"
        >
          <CircularProgress size={24} />
        </LoadingStyled>
      }
    />
  );

  const warning = (
    <AlertStyled isProfilePage={albumId} size={size} severity="warning">
      {i18n.formatMessage({ id: 'config_not_found' })}
    </AlertStyled>
  );

  const itemAddPhotoAlbum = dataSource && albumId ? formAddPhotoAlbum : warning;

  return itemAddPhotoAlbum;
};

export default AddPhotoAlbumDetail;
