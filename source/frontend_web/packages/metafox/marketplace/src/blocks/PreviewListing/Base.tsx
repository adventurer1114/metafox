/* eslint-disable max-len */
import { RouteLink as Link, useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent, ScrollContainer } from '@metafox/layout';
import { getCreateMarketplaceFormValueSelector } from '@metafox/marketplace/selectors';
import { LineIcon, UserAvatar } from '@metafox/ui';
import { Box, styled, Typography } from '@mui/material';
import * as React from 'react';
import { useSelector } from 'react-redux';
import { MarketplaceDetailViewProps } from '../../types';

const name = 'MarketplaceDetail';

const ItemWrapper = styled('div', {
  name,
  slot: 'root',
  shouldForwardProp: prop => prop !== 'isModalView'
})<{ isModalView: boolean }>(({ theme, isModalView }) => ({
  position: 'relative',
  background: theme.palette.background.paper,
  borderRadius: theme.shape.borderRadius,
  margin: 'auto',
  padding: theme.spacing(2),
  [theme.breakpoints.down('xs')]: {
    padding: 0
  },
  ...(isModalView && {
    padding: theme.spacing(2, 0)
  })
}));
const ListingHeader = styled('div', { name, slot: 'listingHeader' })(
  ({ theme }) => ({
    display: 'flex',
    [theme.breakpoints.down('sm')]: {
      display: 'block',
      flexDirection: 'column'
    }
  })
);
const Images = styled('div', { name, slot: 'images' })(({ theme }) => ({
  width: 400,
  float: 'left',
  [theme.breakpoints.down('sm')]: {
    width: '100%',
    float: 'none'
  }
}));
const Container = styled('div', { name, slot: 'container' })(({ theme }) => ({
  flexGrow: 1,
  [theme.breakpoints.down('xs')]: {
    padding: theme.spacing(2, 0)
  }
}));
const InnerContainer = styled('div', { name, slot: 'InnerContainer' })(
  ({ theme }) => ({
    position: 'relative',
    padding: theme.spacing(0, 0, 2, 2),
    [theme.breakpoints.down('sm')]: {
      padding: 0
    }
  })
);
const Price = styled('div', { name, slot: 'price' })(({ theme }) => ({
  color: theme.palette.error.main,
  fontSize: '1.125rem',
  marginBottom: theme.spacing(0.5),
  fontWeight: theme.typography.fontWeightBold
}));
const ItemOuter = styled('div', { name, slot: 'itemOuter' })(({ theme }) => ({
  display: 'flex',
  flexDirection: 'column',
  [theme.breakpoints.down('sm')]: {
    flexDirection: 'column-reverse'
  }
}));
const Info = styled('div', { name, slot: 'info' })(({ theme }) => ({
  color: theme.palette.text.primary,
  fontSize: theme.mixins.pxToRem(15),
  '& p': {
    margin: theme.spacing(1.5, 0)
  }
}));
const OwnerWrapper = styled('div', { name, slot: 'ownerWrapper' })(
  ({ theme }) => ({
    display: 'flex',
    justifyContent: 'space-between',
    alignItems: 'center',
    [theme.breakpoints.down('sm')]: {
      flexDirection: 'column'
    }
  })
);
const Owner = styled('div', { name, slot: 'owner' })(({ theme }) => ({
  overflow: 'hidden',
  padding: theme.spacing(3.5, 0),
  display: 'flex',
  alignItems: 'center',
  width: '100%',
  [theme.breakpoints.down('sm')]: {
    padding: theme.spacing(2, 0, 0, 0)
  }
}));
const ProfileLink = styled(Link, { name, slot: 'profileLink' })(
  ({ theme }) => ({
    color: theme.palette.text.primary,
    fontSize: theme.mixins.pxToRem(15),
    fontWeight: theme.typography.fontWeightBold,
    '&:hover': {
      textDecoration: 'underline'
    }
  })
);
const Country = styled('div', { name, slot: 'country' })(({ theme }) => ({
  color: theme.palette.text.secondary,
  fontSize: theme.mixins.pxToRem(15),
  marginTop: theme.spacing(1)
}));

export default function MarketplaceDetail(props: MarketplaceDetailViewProps) {
  const { useSession } = useGlobal();
  const { user } = useSession();

  const { values } = useSelector(getCreateMarketplaceFormValueSelector) || {};
  const { title, price, text, location } = values || {};

  return (
    <Block>
      <BlockContent>
        <ItemWrapper isModalView={false}>
          <ListingHeader>
            <Images></Images>
            <Container>
              <Box position="relative">
                <InnerContainer>
                  <Box my={1.5} overflow="hidden">
                    <Typography
                      component="h1"
                      variant="h3"
                      color="text.primary"
                      fontWeight="bold"
                    >
                      {title}
                    </Typography>
                  </Box>
                  <Price children={price} />
                  <ItemOuter>
                    <Box>
                      <Info>
                        <ScrollContainer autoHeight autoHeightMax={275}>
                          <HtmlViewer html={text} />
                        </ScrollContainer>
                        {location?.address ? (
                          <Country>
                            <LineIcon icon={'ico-checkin-o'} />
                            <Box component={'span'} py={0} px={1.5}>
                              {location?.address}
                            </Box>
                          </Country>
                        ) : null}
                      </Info>
                    </Box>
                    <OwnerWrapper>
                      <Owner>
                        <Box sx={{ float: 'left' }} mr={2}>
                          <UserAvatar user={user} size={48} />
                        </Box>
                        <Box overflow="hidden" flexGrow="1">
                          <ProfileLink
                            to={`/${user?.user_name}`}
                            children={user?.full_name}
                            hoverCard={`/user/${user?.id}`}
                          />
                        </Box>
                      </Owner>
                    </OwnerWrapper>
                  </ItemOuter>
                </InnerContainer>
              </Box>
            </Container>
          </ListingHeader>
        </ItemWrapper>
      </BlockContent>
    </Block>
  );
}
