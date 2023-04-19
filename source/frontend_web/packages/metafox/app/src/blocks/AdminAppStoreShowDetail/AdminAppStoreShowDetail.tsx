import React, { useCallback } from 'react';
import { StoreProductItemShape } from '@metafox/core/types';
import { Flag } from '@metafox/ui';
import { useGetItem, useGlobal } from '@metafox/framework';
import { Box, Typography, CardMedia, Grid, styled } from '@mui/material';
import { getImageSrc } from '@metafox/utils';
import Slider from 'react-slick';
import 'slick-carousel/slick/slick-theme.css';
import 'slick-carousel/slick/slick.css';
import MoreInfo from './MoreInfo/Base';
import Summary from './Summary';
import Rating from './Rating';
import Purchase from './Purchase';
import { LoadingButton } from '@mui/lab';

const GridStyled = styled(Grid, { name: 'gridStyled' })(({ theme }) => ({
  minWidth: '100vh',
  marginLeft: 0,
  marginTop: 0
}));

const GridItemStyled = styled(Grid, { name: 'gridItemStyled' })(
  ({ theme }) => ({
    '& > div:not(:first-of-type)': {
      marginTop: theme.spacing(3)
    }
  })
);

const CardContentStyled = styled(Box, { name: 'cardContentStyled' })(
  ({ theme }) => ({
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'space-between',
    width: '100%',
    paddingLeft: theme.spacing(2)
  })
);

const CardImageWrapper = styled(Box, { name: 'cardImageWrapper' })(
  ({ theme }) => ({ display: 'flex', alignItems: 'center', pl: 1, pb: 1 })
);

const SliderWrapper = styled(Box, { name: 'sliderWrapper' })(({ theme }) => ({
  margin: theme.spacing(2, 0),
  border: '1px solid',
  borderColor: theme.palette.border.secondary
}));

export const ProductContext = React.createContext<StoreProductItemShape>(null);

export default function AdminAppStoreShowDetail() {
  const { usePageParams, dispatch, assetUrl, i18n, jsxBackend } = useGlobal();

  const { identity, id } = usePageParams();

  const item = useGetItem<StoreProductItemShape>(identity);

  const handleInstall = useCallback(() => {
    if (item?.is_installing) return;

    dispatch({ type: 'app/store/install', payload: { identity } });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [id, item]);

  if (!item)
    return (
      <Box
        display="flex"
        sx={{
          height: '100px',
          width: '100%',
          backgroundColor: 'background.paper',
          justifyContent: 'center'
        }}
      >
        {jsxBackend.render({ component: 'form.DefaultLoading' })}
      </Box>
    );

  const { author, is_installing } = item;

  const settings = {
    infinite: true,
    speed: 400,
    slidesToShow: 1,
    slidesToScroll: 1,
    autoplay: true
  };

  const images = item?.images?.length
    ? item?.images
    : [assetUrl('layout.image_no_results')];

  return (
    <ProductContext.Provider value={item}>
      <GridStyled container spacing={2} sx={{ background: '#fff' }}>
        <Grid xs={8} item sx={{ pr: 2 }}>
          <Box sx={{ display: 'flex' }}>
            <Box sx={{ display: 'flex', width: '100%' }}>
              <CardImageWrapper>
                <CardMedia
                  component="img"
                  sx={{ width: 120, height: 120, borderRadius: '30px' }}
                  image={getImageSrc(item?.icon || item?.image, '500')}
                />
              </CardImageWrapper>
              <CardContentStyled>
                <Box sx={{ maxWidth: '70%' }}>
                  <Flag type={'is_featured'} value={item?.is_featured} />
                  <Typography
                    component="div"
                    variant="h1"
                    sx={{ fontWeight: 500 }}
                    color="#555555"
                  >
                    {item?.name}
                  </Typography>
                  <Typography
                    variant="body2"
                    color="text.secondary"
                    component="div"
                  >
                    {item?.categories?.map((category, index) => (
                      <span key={index}>
                        {category.name} {'•'}
                      </span>
                    ))}{' '}
                    {author.name}
                  </Typography>
                </Box>
                <Box sx={{ textAlign: 'center' }}>
                  <LoadingButton
                    disableRipple
                    disableFocusRipple
                    onClick={handleInstall}
                    variant="contained"
                    disabled={!item?.can_install || is_installing}
                    size="small"
                    loading={is_installing}
                  >
                    {i18n.formatMessage({ id: 'install' })}
                  </LoadingButton>
                  <Typography
                    color="text.secondary"
                    variant="body2"
                    sx={{ py: 1 }}
                  >
                    {i18n.formatMessage(
                      { id: 'total_installed' },
                      { value: item?.total_installed }
                    )}
                  </Typography>
                </Box>
              </CardContentStyled>
            </Box>
          </Box>
          <SliderWrapper>
            <Slider {...settings}>
              {images.map((image, index) => (
                <CardMedia
                  key={index}
                  component="img"
                  sx={{ height: '320px', width: '100%', objectFit: 'contain' }}
                  image={getImageSrc(image, '1024')}
                />
              ))}
            </Slider>
          </SliderWrapper>

          <MoreInfo />
        </Grid>
        <GridItemStyled xs={4} item>
          <Purchase />
          <Rating />
          <Summary />
        </GridItemStyled>
      </GridStyled>
    </ProductContext.Provider>
  );
}
