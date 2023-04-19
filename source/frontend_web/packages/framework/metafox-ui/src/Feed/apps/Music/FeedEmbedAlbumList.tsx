/**
 * @type: ui
 * name: feedAlbum.view.list.embedItem
 */
import { ImageRatio, ItemShape } from '@metafox/ui';
import * as React from 'react';
import FeedEmbedAlbumTemplate from './FeedEmbedAlbumTemplate';

type Props = {
  title?: string;
  description?: string;
  link?: string;
  host?: string;
  image?: string;
  mediaRatio?: ImageRatio;
  price?: string;
  displayStatistic?: string;
  maxLinesTitle?: number;
  maxLinesDescription?: number;
  highlightSubInfo?: string;
  variant?: 'grid' | 'list';
} & ItemShape;

const FeedArticleList = (props: Props) => (
  <FeedEmbedAlbumTemplate
    {...props}
    variant={'list'}
    widthImage="200px"
    mediaRatio={'11'}
  />
);

export default FeedArticleList;
