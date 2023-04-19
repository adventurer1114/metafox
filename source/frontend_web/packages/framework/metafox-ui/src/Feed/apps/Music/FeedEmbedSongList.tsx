/**
 * @type: ui
 * name: feedSong.view.list.embedItem
 */
import { ImageRatio, ItemShape } from '@metafox/ui';
import { Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import clsx from 'clsx';
import * as React from 'react';
import FeedEmbedSongCard from './FeedEmbedSongCard';
import FeedEmbedSongCardMini from './FeedEmbedSongCardMini';

type Props = {
  link?: string;
  image?: string;
  widthImage?: string;
  mediaRatio?: ImageRatio;
  displayStatistic?: string;
  maxLinesTitle?: number;
  variant?: 'grid' | 'list';
  songs?: Array<SongItemProps>;
  category?: string;
  album?: AlbumProps;
} & ItemShape;

type AlbumProps = {
  title: string;
  link: string;
};

type SongItemProps = {
  id?: string;
  title?: string;
  href?: string;
  resource_name?: string;
  image?: string;
  song_path?: string;
  statistic?: Record<string, number>;
};
const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      listing: {
        display: 'block'
      },
      oneType: {},
      multipleType: {
        borderRadius: theme.shape.borderRadius,
        border: theme.mixins.border('secondary'),
        padding: theme.spacing(2),
        '& $item:not(:last-child)': {
          paddingBottom: theme.spacing(2),
          marginBottom: theme.spacing(2),
          borderBottom: theme.mixins.border('secondary')
        }
      },
      item: {}
    }),
  { name: 'MuiFeedEmbedSongList' }
);

const FeedEmbedSongList = (props: Props) => {
  const { songs = [] } = props;
  const classes = useStyles();

  return (
    <div
      className={clsx(
        classes.listing,
        1 === songs.length ? classes.oneType : classes.multipleType
      )}
    >
      {songs.length &&
        songs.map((item, index) => (
          <div className={classes.item} key={index.toString()}>
            {1 === songs.length ? (
              <FeedEmbedSongCard {...item} />
            ) : (
              <FeedEmbedSongCardMini {...item} />
            )}
          </div>
        ))}
    </div>
  );
};

export default FeedEmbedSongList;
