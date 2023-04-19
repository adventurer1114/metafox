/**
 * @type: ui
 * name: feedEvent.view.grid.embedItem
 */
import { ItemShape } from '@metafox/ui';
import * as React from 'react';
import FeedEmbedEventTemplate from './FeedEmbedEventTemplate';

type Props = {
  title?: string;
  description?: string;
  link?: string;
  image?: string;
  displayStatistic?: string;
  maxLinesTitle?: number;
  maxLinesDescription?: number;
  highlightSubInfo?: string;
  variant?: 'grid' | 'list';
} & ItemShape;

const FeedEmbedEventGrid = (props: Props) => (
  <FeedEmbedEventTemplate
    {...props}
    variant={'grid'}
    mediaRatio={'fixed'}
    displayStatistic={'total_attending'}
  />
);

export default FeedEmbedEventGrid;
