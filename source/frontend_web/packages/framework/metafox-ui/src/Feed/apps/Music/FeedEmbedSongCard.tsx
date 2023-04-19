import { Link } from '@metafox/framework';
import { Flag, Statistic, TruncateText } from '@metafox/ui';
import { Theme } from '@mui/material';
import Box from '@mui/material/Box';
import { createStyles, makeStyles } from '@mui/styles';
import * as React from 'react';
import MinorInformation from '../../../MinorInformation';
import FeedEmbedCardBlock from '../../components/FeedEmbedCardBlock';

const useStyles = makeStyles(
  (theme: Theme) =>
    createStyles({
      item: {
        display: 'block'
      },
      itemOuter: {
        display: 'flex',
        borderRadius: '8px',
        border: theme.mixins.border('secondary'),
        backgroundColor: theme.mixins.backgroundColor('paper'),
        overflow: 'hidden'
      },
      title: {
        '& a': {
          color: theme.palette.text.primary
        }
      },
      hostLink: {
        color: theme.palette.text.secondary
      },
      subInfo: {
        textTransform: 'uppercase'
      },
      itemInner: {
        flex: 1,
        minWidth: 0,
        padding: theme.spacing(3),
        display: 'flex',
        flexDirection: 'column'
      },
      wrapperInfoFlag: {
        marginTop: 'auto'
      },
      flagWrapper: {
        marginLeft: 'auto'
      },
      information: {
        color: theme.palette.text.secondary
      },
      category: {},
      album: {}
    }),
  { name: 'MuiFeedEmbedSongCard' }
);

const FeedEmbedSongCard = props => {
  const {
    title,
    image,
    href: link,
    statistic = { total_play: 100 },
    is_featured,
    displayStatistic = 'total_play',
    widthImage = '200px',
    mediaRatio = '11',
    variant = 'list',
    category = 'Category Name',
    album = {
      title: 'Album title',
      link: 'https://mobileapi.phpfox.com/music/album/23/test-album/'
    }
  } = props;

  const classes = useStyles();

  return (
    <FeedEmbedCardBlock
      image={image}
      variant={variant}
      widthImage={widthImage}
      link={link}
      mediaRatio={mediaRatio}
    >
      <div className={classes.itemInner}>
        <Box mb={1.5} fontWeight="bold" className={classes.title}>
          <Link to={link}>
            <TruncateText variant="h4" lines={2}>
              {title}
            </TruncateText>
          </Link>
        </Box>
        <div className={classes.information}>
          <Box className={classes.category} mb={1.5}>
            <MinorInformation label={'genre'} values={[{ title: category }]} />
          </Box>
          <Box className={classes.album}>
            <MinorInformation label={'album'} values={[{ ...album }]} />
          </Box>
        </div>
        <Box
          className={classes.wrapperInfoFlag}
          display="flex"
          justifyContent="space-between"
          alignItems="flex-end"
        >
          <Statistic
            values={statistic}
            display={displayStatistic}
            fontStyle={'minor'}
          />
          <div className={classes.flagWrapper}>
            {is_featured && <Flag type={'is_featured'} />}
          </div>
        </Box>
      </div>
    </FeedEmbedCardBlock>
  );
};

export default FeedEmbedSongCard;
