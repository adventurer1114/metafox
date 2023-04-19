import { Link, useGlobal } from '@metafox/framework';
import {
  Flag,
  ImageRatio,
  ItemShape,
  Statistic,
  TruncateText
} from '@metafox/ui';
import { Theme } from '@mui/material';
import Box from '@mui/material/Box';
import { createStyles, makeStyles } from '@mui/styles';
import * as React from 'react';
import FeedEmbedCardBlock from '../../components/FeedEmbedCardBlock';

type Props = {
  title?: string;
  link?: string;
  host?: string;
  image?: string;
  widthImage?: string;
  heightImage?: string;
  mediaRatio?: ImageRatio;
  displayStatistic?: string;
  maxLinesTitle?: number;
  highlightSubInfo?: string;
  variant?: 'grid' | 'list';
  year?: string;
} & ItemShape;

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
      subInfo: {
        textTransform: 'uppercase'
      },
      itemInner: {
        flex: 1,
        minWidth: 0,
        padding: theme.spacing(2),
        display: 'flex',
        flexDirection: 'column'
      },
      wrapperInfoFlag: {
        marginTop: 'auto'
      },
      flagWrapper: {
        marginLeft: 'auto'
      },
      year: {
        color: theme.palette.text.secondary
      }
    }),
  { name: 'MuiFeedEmbedProductTemplate' }
);

const FeedEmbedProductTemplate = (props: Props) => {
  const {
    title,
    maxLinesTitle = 2,
    link,
    is_featured,
    year,
    statistic,
    displayStatistic = 'total_play'
  } = props;

  const classes = useStyles();
  const { i18n } = useGlobal();

  return (
    <FeedEmbedCardBlock
      {...props}
      playerOverlay
      playerOverlayProps={{
        size: 'sm'
      }}
    >
      <div className={classes.itemInner}>
        {link ? (
          <Box mb={1} fontWeight="bold" className={classes.title}>
            <Link to={link}>
              <TruncateText variant="h4" lines={maxLinesTitle}>
                {title}
              </TruncateText>
            </Link>
          </Box>
        ) : (
          <Box className={classes.title} mb={1}>
            <TruncateText variant="h4" lines={2}>
              {title}
            </TruncateText>
          </Box>
        )}
        {year && (
          <Box className={classes.year} mb={1}>
            {i18n.formatMessage({ id: 'year' })}:{year}
          </Box>
        )}
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

export default FeedEmbedProductTemplate;
