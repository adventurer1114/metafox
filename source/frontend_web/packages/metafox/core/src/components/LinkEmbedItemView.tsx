/**
 * @type: embedView
 * name: link.embedItem.insideFeedItem
 */
import { getItemSelector, GlobalState } from '@metafox/framework';
import {
  FeedEmbedCard,
  FeedEmbedCardMedia,
  FeedEmbedDescription,
  FeedEmbedHost,
  FeedEmbedTitle,
  ItemShape
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import React from 'react';
import { useSelector } from 'react-redux';

type Props = {
  identity: string;
  description: string;
} & ItemShape;

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      host: {
        textTransform: 'uppercase'
      },
      inner: {
        flex: 1,
        minWidth: 0,
        padding: theme.spacing(3),
        display: 'flex',
        flexDirection: 'column'
      }
    }),
  { name: 'MuiLinkEmbedItemView' }
);

const LinkEmbedItemView = ({ identity }: Props) => {
  const item = useSelector((state: GlobalState) =>
    getItemSelector(state, identity)
  );

  const { title, description, link, image, host } = item;
  const classes = useStyles();
  const imgSrc = getImageSrc(image, '200');

  return (
    <FeedEmbedCard variant="list" bottomSpacing="normal">
      {imgSrc ? (
        <FeedEmbedCardMedia
          image={getImageSrc(image, '200')}
          widthImage="200px"
          mediaRatio="11"
        />
      ) : null}
      <div className={classes.inner} data-testid="embedview">
        <FeedEmbedTitle
          title={title}
          linkProps={{
            href: link,
            target: '_blank'
          }}
        />
        <FeedEmbedDescription content={description} />
        <FeedEmbedHost host={host} />
      </div>
    </FeedEmbedCard>
  );
};

export default LinkEmbedItemView;
