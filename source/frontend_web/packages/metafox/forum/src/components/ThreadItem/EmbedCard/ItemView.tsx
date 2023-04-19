import { EmbedThreadItemInFeedItemProps } from '@metafox/forum/types';
import * as React from 'react';
import ItemViewBase from './Base';

export default function ItemView({ item }: EmbedThreadItemInFeedItemProps) {
  if (!item) return null;

  const {
    title,
    description,
    statistic,
    link,
    module_name,
    short_description
  } = item;

  return (
    <ItemViewBase
      title={title}
      description={description}
      short_description={short_description}
      statistic={statistic}
      link={link}
      appName={module_name}
    />
  );
}

ItemView.displayName = 'ForumThread_EmbedCard';
