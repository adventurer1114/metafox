import {
  RouteLink as Link,
  useGlobal,
  getItemSelector
} from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import {
  FeaturedFlag,
  FormatDate,
  Image,
  LineIcon,
  PendingFlag,
  SponsorFlag,
  UserAvatar,
  AttachmentItem,
  Tags,
  DotSeparator,
  PrivacyIcon,
  Statistic,
  ItemAction
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, Button, styled, Typography, IconButton } from '@mui/material';
import * as React from 'react';
import { MarketplaceDetailViewProps } from '../../types';
import ListingImages from './ListingImages';
import { useSelector } from 'react-redux';
import LinkProfile from '@metafox/feed/components/FeedItemView/ProfileLink';
import {
  BOUNDS_EAST,
  BOUNDS_NORTH,
  BOUNDS_SOUTH,
  BOUNDS_WEST
} from '@metafox/marketplace';

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
  marginRight: theme.spacing(2),
  [theme.breakpoints.down('sm')]: {
    width: '100%',
    float: 'none'
  }
}));
const ImageStyled = styled(Image, { name, slot: 'image' })(({ theme }) => ({
  width: 400,
  height: 400,
  [theme.breakpoints.down('sm')]: {
    marginRight: 0,
    width: '100%',
    float: 'none'
  }
}));
const Container = styled('div', { name, slot: 'container' })(({ theme }) => ({
  flexGrow: 1,
  [theme.breakpoints.down('sm')]: {
    padding: theme.spacing(2, 2, 0, 2)
  }
}));
const InnerContainer = styled('div', { name, slot: 'InnerContainer' })(
  ({ theme }) => ({
    position: 'relative'
  })
);
const Category = styled(Link, { name, slot: 'Category' })(({ theme }) => ({
  fontSize: theme.mixins.pxToRem(13),
  color: theme.palette.primary.main,
  textTransform: 'capitalize',
  display: 'inline-block',
  '&:hover': {
    textDecoration: 'underline'
  }
}));
const FlagWrapper = styled('div', { name, slot: 'FlagWrapper' })(
  ({ theme }) => ({
    display: 'inline-flex',
    margin: theme.spacing(0, 0, 0, -0.5),
    float: 'left'
  })
);
const Price = styled('div', { name, slot: 'price' })(({ theme }) => ({
  color: theme.palette.error.main,
  fontSize: '1.125rem',
  fontWeight: theme.typography.fontWeightBold
}));
const ItemOuter = styled('div', { name, slot: 'itemOuter' })(({ theme }) => ({
  display: 'flex',
  flexDirection: 'column',
  [theme.breakpoints.down('sm')]: {
    flexDirection: 'column-reverse'
  }
}));
const Info = styled(Box, { name, slot: 'info' })(({ theme }) => ({
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
    marginBottom: theme.spacing(2),
    [theme.breakpoints.down('sm')]: {
      flexDirection: 'column',
      alignItems: 'flex-start'
    }
  })
);
const Owner = styled('div', { name, slot: 'owner' })(({ theme }) => ({
  overflow: 'hidden',
  padding: theme.spacing(3.5, 0),
  display: 'flex',
  alignItems: 'center',
  flex: 1,
  minWidth: 0,
  [theme.breakpoints.down('sm')]: {
    padding: theme.spacing(1, 0, 1, 0)
  }
}));
const ProfileLink = styled(Link, { name, slot: 'profileLink' })(
  ({ theme }) => ({
    paddingRight: theme.spacing(0.5),
    color: theme.palette.text.primary,
    fontSize: theme.mixins.pxToRem(15),
    fontWeight: theme.typography.fontWeightBold,
    '&:hover': {
      textDecoration: 'underline'
    }
  })
);

const ListingActions = styled('div', { name, slot: 'listingActions' })(
  ({ theme }) => ({
    display: 'flex',
    marginTop: theme.spacing(2),
    marginBottom: theme.spacing(2),
    flexFlow: 'wrap',
    '& button': {
      marginRight: theme.spacing(1)
    }
  })
);

const AttachmentTitle = styled('div', { name, slot: 'attachmentTitle' })(
  ({ theme }) => ({
    fontSize: theme.mixins.pxToRem(18),
    marginTop: theme.spacing(2),
    color: theme.palette.text.secondary,
    fontWeight: theme.typography.fontWeightBold
  })
);
const Attachment = styled('div', { name, slot: 'attachment' })(({ theme }) => ({
  width: '100%',
  display: 'flex',
  flexWrap: 'wrap',
  justifyContent: 'space-between'
}));

const AttachmentItemWrapper = styled('div', {
  name,
  slot: 'attachmentItemWrapper'
})(({ theme }) => ({
  marginTop: theme.spacing(2),
  flexGrow: 0,
  flexShrink: 0,
  flexBasis: 'calc(50% - 8px)',
  minWidth: 300
}));

const Expires = styled('div', {
  name,
  slot: 'Expires'
})(({ theme }) => ({
  display: 'flex',
  marginTop: theme.spacing(2),
  color: theme.palette.error.main
}));

const HeadlineSpan = styled('span', { name: 'HeadlineSpan' })(({ theme }) => ({
  paddingRight: theme.spacing(0.5),
  color: theme.palette.text.secondary
}));

const OwnerStyled = styled(LinkProfile, { name: 'OwnerStyled' })(
  ({ theme }) => ({
    fontWeight: theme.typography.fontWeightBold,
    color: theme.palette.text.primary,
    fontSize: theme.mixins.pxToRem(15),
    '&:hover': {
      textDecoration: 'underline'
    }
  })
);
const LocationStyled = styled('div', { name: 'LocationStyled' })(
  ({ theme }) => ({
    color: theme.palette.text.secondary,
    fontSize: theme.mixins.pxToRem(15),
    marginTop: theme.spacing(3),
    display: 'flex'
  })
);

const LinkStyled = styled(Box, { name: 'LinkStyled' })(({ theme }) => ({
  paddingTop: theme.spacing(0.5),
  cursor: 'pointer',
  fontSize: theme.mixins.pxToRem(13),
  color: theme.palette.primary.main,
  margin: theme.spacing(0, 0.5)
}));

export default function MarketplaceDetail(props: MarketplaceDetailViewProps) {
  const { item, actions, user, identity, handleAction, state, isModalView } =
    props;

  const {
    i18n,
    ItemActionMenu,
    ItemDetailInteraction,
    dispatch,
    useGetItems,
    jsxBackend
  } = useGlobal();
  const attachments = useGetItems(item?.attachments);
  const categories = useGetItems(item?.categories);
  const handlePayment = React.useCallback(() => {
    actions.paymentItem();
  }, [actions]);

  const owner = useSelector((state: GlobalState) =>
    getItemSelector(state, item?.owner)
  );

  if (!item || !user) return null;

  const {
    is_featured,
    is_sponsor,
    title,
    price,
    description,
    short_description,
    extra,
    is_pending,
    image,
    location,
    attach_photos: images,
    user: listingSeller,
    tags,
    is_sold,
    is_expired,
    is_free,
    expires_label
  } = item;

  const cover = getImageSrc(image);

  const PendingCard = jsxBackend.get('core.itemView.pendingReviewCard');

  const chatWithSeller = () => {
    dispatch({
      type: 'chat/room/openChatRoom',
      payload: {
        identity: listingSeller
      }
    });
  };

  const onViewMap = () => {
    const north = location.lat;
    const west = location.lng;
    const east = location.lng;
    const south = location.lat;

    navigate({
      pathname: '/marketplace/search-map/',
      search: `?${BOUNDS_NORTH}=${north}&${BOUNDS_EAST}=${east}&${BOUNDS_WEST}=${west}&${BOUNDS_SOUTH}=${south}`
    });
  };

  return (
    <Block testid={`detailview ${item.resource_name}`}>
      <BlockContent>
        <ItemWrapper isModalView={isModalView}>
          <ListingHeader>
            {images?.length || cover ? (
              <Images>
                {images ? (
                  <ListingImages images={images} alt={title} />
                ) : (
                  <ImageStyled aspectRatio={'11'} src={cover} alt={'photo'} />
                )}
              </Images>
            ) : null}
            <Container>
              <Box position="relative">
                <InnerContainer>
                  {is_pending && PendingCard ? (
                    <Box sx={{ margin: 2 }}>
                      <PendingCard sx item={item} />
                    </Box>
                  ) : null}
                  {categories
                    ? categories.map(category => (
                        <Category
                          to={`/marketplace/category/${category.id}`}
                          title={category.name}
                          key={category.name}
                        >
                          {category.name}
                        </Category>
                      ))
                    : null}
                  <Box my={1.5} overflow="hidden">
                    <FlagWrapper>
                      {is_pending && (
                        <PendingFlag variant="detailView" value={is_pending} />
                      )}
                      {is_featured ? (
                        <FeaturedFlag
                          variant="detailView"
                          value={is_featured}
                        />
                      ) : null}
                      {is_sponsor ? (
                        <SponsorFlag variant="detailView" value={is_sponsor} />
                      ) : null}
                    </FlagWrapper>
                    <Typography
                      component="h1"
                      variant="h3"
                      color="text.primary"
                      fontWeight="bold"
                    >
                      {title}
                    </Typography>
                  </Box>
                  <Box sx={{ display: 'flex', alignItems: 'center' }}>
                    {is_sold || is_expired ? (
                      <Box mr={1}>
                        {jsxBackend.render({
                          component: 'marketplace.ui.soldLabel',
                          props: {
                            label: is_expired ? 'expired' : ''
                          }
                        })}
                      </Box>
                    ) : null}
                    {is_free ? (
                      <Price> {i18n.formatMessage({ id: 'free' })} </Price>
                    ) : (
                      <Price children={price} />
                    )}
                  </Box>
                  {extra?.can_payment || extra?.can_invite ? (
                    <ListingActions>
                      {extra?.can_payment ? (
                        <Button
                          size="medium"
                          color="primary"
                          variant="contained"
                          onClick={handlePayment}
                          startIcon={<LineIcon icon={'ico-money-bag-o'} />}
                        >
                          {i18n.formatMessage({ id: 'buy_now' })}
                        </Button>
                      ) : null}
                      {extra?.can_invite ? (
                        <ItemActionMenu
                          menuName="itemInviteMenu"
                          state={state}
                          handleAction={handleAction}
                          control={
                            <IconButton
                              color="primary"
                              variant={'outlined-square'}
                              size="medium"
                            >
                              <LineIcon icon={'ico-envelope'} />
                            </IconButton>
                          }
                        />
                      ) : null}
                      <ItemActionMenu
                        menuName="detailActionMenu"
                        identity={identity}
                        state={state}
                        handleAction={handleAction}
                        control={
                          <IconButton
                            color="primary"
                            variant={'outlined-square'}
                            size="medium"
                          >
                            <LineIcon icon={'ico-dottedmore-o'} />
                          </IconButton>
                        }
                      />
                    </ListingActions>
                  ) : (
                    <ItemAction
                      sx={{ position: 'absolute', top: 0, right: '-8px' }}
                    >
                      <ItemActionMenu
                        identity={identity}
                        icon={'ico-dottedmore-vertical-o'}
                        state={state}
                        menuName="detailActionMenu"
                        handleAction={handleAction}
                        size="smaller"
                      />
                    </ItemAction>
                  )}
                  <ItemOuter>
                    <Box>
                      <Info mt={2}>
                        <HtmlViewer html={short_description || ''} />
                      </Info>
                      {expires_label ? (
                        <Expires>
                          {' '}
                          {i18n.formatMessage({ id: expires_label })}{' '}
                        </Expires>
                      ) : null}
                      {location?.lat && location?.lng && (
                        <LocationStyled>
                          <LineIcon icon="ico-checkin-o" />
                          <Box ml={1.5}>
                            <div>{location?.address}</div>
                            <Box sx={{ display: 'flex' }}>
                              <LinkStyled component="span" onClick={onViewMap}>
                                {i18n.formatMessage({ id: 'view_on_map' })}
                              </LinkStyled>
                              {'·'}
                              <LinkStyled
                                component="a"
                                href={`http://maps.google.com/?q=${location?.address}`}
                                target="_blank"
                              >
                                {i18n.formatMessage({
                                  id: 'view_on_google_maps'
                                })}
                              </LinkStyled>
                            </Box>
                          </Box>
                        </LocationStyled>
                      )}
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
                          {owner?.resource_name !== user?.resource_name && (
                            <HeadlineSpan>
                              {i18n.formatMessage(
                                {
                                  id: 'to_parent_user'
                                },
                                {
                                  icon: () => (
                                    <LineIcon icon="ico-caret-right" />
                                  ),
                                  parent_user: () => (
                                    <OwnerStyled user={owner} />
                                  )
                                }
                              )}
                            </HeadlineSpan>
                          )}
                          <DotSeparator
                            sx={{ color: 'text.secondary', mt: 0.5 }}
                          >
                            <FormatDate
                              value={item.creation_date}
                              format="MMMM DD, yyyy"
                              data-testid="from-date"
                            />
                            <Statistic
                              values={item.statistic}
                              display={'total_view'}
                              component={'span'}
                              skipZero={false}
                            />
                            <Box
                              component="span"
                              sx={{
                                transform: 'translateY(1px)',
                                display: 'inline-block'
                              }}
                            >
                              <PrivacyIcon
                                value={item?.privacy}
                                item={item?.privacy_detail}
                              />
                            </Box>
                          </DotSeparator>
                        </Box>
                      </Owner>
                      {extra?.can_message ? (
                        <Button
                          size="medium"
                          color="primary"
                          variant="outlined"
                          onClick={chatWithSeller}
                          startIcon={<LineIcon icon={'ico-comment-o'} />}
                          sx={{ mt: { sm: 0, xs: 2 } }}
                        >
                          {i18n.formatMessage({ id: 'messages' })}
                        </Button>
                      ) : null}
                    </OwnerWrapper>
                  </ItemOuter>
                </InnerContainer>
              </Box>
            </Container>
          </ListingHeader>
          <Box sx={{ p: { sm: 0, xs: 2 } }}>
            {description ? (
              <Info>
                <HtmlViewer html={description || ''} />
              </Info>
            ) : null}
            <Tags tags={tags} module_name={item?.module_name} />
            {attachments?.length > 0 && (
              <>
                <AttachmentTitle>
                  {i18n.formatMessage({ id: 'attachments' })}
                </AttachmentTitle>
                <Attachment>
                  {attachments.map(item => (
                    <AttachmentItemWrapper key={item.id.toString()}>
                      <AttachmentItem
                        fileName={item.file_name}
                        downloadUrl={item.download_url}
                        isImage={item.is_image}
                        fileSizeText={item.file_size_text}
                        size="large"
                        image={item?.image}
                      />
                    </AttachmentItemWrapper>
                  ))}
                </Attachment>
              </>
            )}
            <ItemDetailInteraction
              identity={identity}
              state={state}
              handleAction={handleAction}
            />
          </Box>
        </ItemWrapper>
      </BlockContent>
    </Block>
  );
}
