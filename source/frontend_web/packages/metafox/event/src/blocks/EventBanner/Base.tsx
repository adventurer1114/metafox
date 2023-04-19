import { useGlobal } from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { Container } from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import React from 'react';
import useStyles from './styles';

export default function EventBanner({ item, blockProps }: any) {
  const classes = useStyles();
  const { assetUrl, jsxBackend } = useGlobal();
  const PendingGroupPreview = jsxBackend.get(
    'event.itemView.pendingReviewEventCard'
  );

  if (!item) return null;

  const image = getImageSrc(
    item.image,
    '1024',
    assetUrl('event.cover_no_image')
  );

  return (
    <Block>
      <BlockContent>
        <div className={classes.root}>
          <div
            className={classes.bgBlur}
            style={{
              backgroundImage: `url(${image})`
            }}
          />
          <Container maxWidth={'md'} disableGutters>
            <div
              className={classes.bgCover}
              style={{ backgroundImage: `url(${image})` }}
            ></div>
          </Container>
        </div>
        <Container sx={{ pt: 2 }} maxWidth={'md'} disableGutters>
          <PendingGroupPreview item={item} />
        </Container>
      </BlockContent>
    </Block>
  );
}

EventBanner.LoadingSkeleton = () => null;
EventBanner.displayName = 'EventBanner';
