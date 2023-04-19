/**
 * @type: ui
 * name: feedEvent.view.list.embedItem
 */
import { FeedEmbedObjectViewProps } from '@metafox/ui';
import * as React from 'react';
import FeedEmbedEventTemplate from './FeedEmbedEventTemplate';

type Props = FeedEmbedObjectViewProps;

const FeedEmbedEventGrid = (props: Props) => (
  <FeedEmbedEventTemplate
    {...props}
    variant={'list'}
    mediaRatio={'11'}
    displayStatistic={'total_attending'}
  />
);

export default FeedEmbedEventGrid;
