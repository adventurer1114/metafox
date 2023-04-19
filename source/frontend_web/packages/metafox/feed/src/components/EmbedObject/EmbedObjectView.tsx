import { useGlobal, getItemSelector, GlobalState } from '@metafox/framework';
import { ItemShape } from '@metafox/ui';
import { isArray, isEmpty, isString } from 'lodash';
import { useSelector } from 'react-redux';
import * as React from 'react';
import ItemNotFound from './ItemNotFound';
import { RESOURCE_ALBUM } from '@metafox/photo';

export type FeedEmbedObjectViewProps = {
  feed?: ItemShape;
  embed?: string;
  setVisible?: (value: boolean) => void;
};

export default function FeedEmbedObjectView({
  feed,
  embed,
  setVisible
}: FeedEmbedObjectViewProps) {
  const { jsxBackend } = useGlobal();

  const embedItem = useSelector<GlobalState>(state =>
    getItemSelector(state, embed)
  ) as ItemShape & { error?: string; message?: string; title?: string };

  if (embedItem?.error)
    return (
      <ItemNotFound description={embedItem.message} title={embedItem.title} />
    );

  if (isArray(embed) && isEmpty(embed)) return <ItemNotFound />;

  if (!feed || !isString(embed)) return null;

  if (
    isArray(embedItem?.items) &&
    isEmpty(embedItem?.items) &&
    embedItem?.resource_name === RESOURCE_ALBUM
  )
    return (
      <ItemNotFound
        description="no_photos_uploaded"
        title="this_album_is_empty"
      />
    );

  const resource = embed.split('.')[2];

  const EmbedObjectView = jsxBackend.get(
    `${resource}.embedItem.insideFeedItem`
  );

  if (!EmbedObjectView) return null;

  return React.createElement(EmbedObjectView, {
    identity: embed,
    feed,
    setVisible
  });
}
