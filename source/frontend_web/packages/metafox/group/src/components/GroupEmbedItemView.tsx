import { useGlobal } from '@metafox/framework';
import { EmbedObjectShape, FeedEmbedObjectViewProps } from '@metafox/ui';
import * as React from 'react';

export type GroupEmbedItemViewProps = FeedEmbedObjectViewProps<
  EmbedObjectShape & {
    membership: number;
    location?: string;
    start_time: string;
  }
>;

const GroupEmbedItemView = ({
  feed,
  embed,
  itemView = 'feedPage.view.list.embedItem'
}: GroupEmbedItemViewProps) => {
  const { title, location, image, statistic } = embed;
  const { jsxBackend } = useGlobal();
  const ItemView = jsxBackend.get(itemView);

  return (
    <ItemView
      title={title}
      description={location}
      image={image}
      statistic={statistic}
      link={feed.link}
      variant={'grid'}
      mediaRatio={'fixed'}
    />
  );
};

export default GroupEmbedItemView;
