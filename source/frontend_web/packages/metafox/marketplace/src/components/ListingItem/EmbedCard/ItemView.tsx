import { Link, useGlobal } from '@metafox/framework';
import { EmbedListingInFeedItemProps } from '@metafox/marketplace';
import {
  FeedEmbedCard,
  FeedEmbedCardMedia,
  Flag,
  LineIcon,
  TruncateText
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, styled } from '@mui/material';
import React from 'react';

const name = 'EmbedListingInFeedItemView';

const ItemInner = styled('div', { name, slot: 'itemInner' })(({ theme }) => ({
  flex: 1,
  minWidth: 0,
  padding: theme.spacing(2),
  display: 'flex',
  flexDirection: 'column'
}));
const Price = styled('div', { name, slot: 'Price' })(({ theme }) => ({
  fontWeight: theme.typography.fontWeightBold,
  color: theme.palette.error.main
}));

const PriceNotAvailable = styled('div', { name, slot: 'PriceNotAvailable' })(
  ({ theme }) => ({
    color: theme.palette.text.hint,
    fontSize: theme.mixins.pxToRem(13),
    fontWeight: 'normal',
    '& span': {
      marginLeft: theme.spacing(0.5)
    }
  })
);

export default function EmbedListingInFeedItemView({
  item
}: EmbedListingInFeedItemProps) {
  const { i18n, assetUrl } = useGlobal();

  if (!item) return null;

  return (
    <FeedEmbedCard variant="list" bottomSpacing="normal">
      <FeedEmbedCardMedia
        mediaRatio="11"
        widthImage="200px"
        link={item.link ?? ''}
        image={getImageSrc(item.image, '200', assetUrl('marketplace.no_image'))}
      />
      <ItemInner data-testid="embedview">
        <TruncateText variant="h4" lines={2} sx={{ mb: 2 }}>
          <Link to={item.link ?? ''}>{item.title}</Link>
        </TruncateText>
        {item?.short_description && (
          <TruncateText
            component="div"
            variant={'body1'}
            lines={3}
            sx={{ mb: 2 }}
          >
            <div dangerouslySetInnerHTML={{ __html: item.short_description }} />
          </TruncateText>
        )}
        <Box
          display="flex"
          justifyContent="space-between"
          alignItems="flex-end"
        >
          {item?.is_free ? (
            <Price> {i18n.formatMessage({ id: 'free' })} </Price>
          ) : (
            <Price
              children={
                item?.price ?? (
                  <PriceNotAvailable>
                    {i18n.formatMessage({ id: 'price_is_not_available' })}
                    <LineIcon icon="ico-question-circle" />
                  </PriceNotAvailable>
                )
              }
            />
          )}

          <Box marginLeft="auto">
            {item.is_featured ? (
              <Flag data-testid="featured" type={'is_featured'} />
            ) : null}
            {item.is_sponsored_feed ? (
              <Flag data-testid="sponsor" type={'is_sponsor'} />
            ) : null}
          </Box>
        </Box>
      </ItemInner>
    </FeedEmbedCard>
  );
}
