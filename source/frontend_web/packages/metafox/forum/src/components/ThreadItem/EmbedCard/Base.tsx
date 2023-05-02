import { Link, useGlobal } from '@metafox/framework';
import {
  Flag,
  ImageRatio,
  ItemShape,
  TruncateText,
  FeedEmbedCard
} from '@metafox/ui';
import { Box, Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import * as React from 'react';

type Props = {
  title?: string;
  description?: string;
  short_description?: string;
  link?: string;
  host?: string;
  image?: string;
  widthImage?: string;
  heightImage?: string;
  mediaRatio?: ImageRatio;
  price?: string;
  displayStatistic?: string;
  maxLinesTitle?: 1 | 2 | 3;
  maxLinesDescription?: 1 | 2 | 3;
  highlightSubInfo?: string;
  variant?: 'grid' | 'list';
  isComment: boolean;
  appName?: string;
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
          color: theme.palette.text.primary,
          '& h2': {
            fontWeight: theme.typography.fontWeightBold
          }
        }
      },
      description: {
        color: theme.palette.text.secondary,
        '& p': {
          margin: 0
        },
        '& img': {
          maxWidth: '100%'
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
      totalView: {
        color: theme.palette.text.secondary
      },
      price: {
        fontWeight: theme.typography.fontWeightBold,
        color: theme.palette.warning.main
      },
      wrapperInfoFlag: {
        marginTop: 'auto'
      },
      flagWrapper: {
        marginLeft: 'auto'
      }
    }),
  { name: 'MuiFeedEmbedProductTemplate' }
);

const Base = (props: Props) => {
  const {
    title,
    maxLinesTitle = 2,
    short_description,
    maxLinesDescription = 3,
    link,
    is_featured,
    is_sponsored_feed,
    host,
    statistic,
    isComment
  } = props;

  const classes = useStyles();
  const { i18n } = useGlobal();

  const linkParams = !host
    ? { to: link }
    : {
        to: { pathname: link },
        target: '_blank'
      };

  return (
    <FeedEmbedCard variant="grid">
      <div className={classes.itemInner} data-testid="embedview">
        {link ? (
          <Box mb={1} fontWeight={500} className={classes.title}>
            <Link {...linkParams}>
              <TruncateText variant="h5" fontWeight={500} lines={maxLinesTitle}>
                {title}
              </TruncateText>
            </Link>
          </Box>
        ) : (
          <Box className={classes.title} mb={1} fontWeight={500}>
            <TruncateText variant="h5" fontWeight={500} lines={maxLinesTitle}>
              {title}
            </TruncateText>
          </Box>
        )}
        {!isComment ? (
          <>
            <Box className={classes.description} mb={2}>
              <TruncateText variant={'body1'} lines={maxLinesDescription}>
                <div dangerouslySetInnerHTML={{ __html: short_description }} />
              </TruncateText>
            </Box>
            <Box
              className={classes.wrapperInfoFlag}
              display="flex"
              justifyContent="space-between"
              alignItems="flex-end"
            >
              {statistic?.total_comment !== null ? (
                <div className={classes.totalView}>
                  {i18n.formatMessage(
                    { id: 'total_reply' },
                    { value: statistic?.total_comment }
                  )}
                </div>
              ) : null}
              <div className={classes.flagWrapper}>
                {is_featured ? (
                  <Flag data-testid="featured" type={'is_featured'} />
                ) : null}
                {is_sponsored_feed ? (
                  <Flag data-testid="sponsor" type={'is_sponsor'} />
                ) : null}
              </div>
            </Box>
          </>
        ) : null}
      </div>
    </FeedEmbedCard>
  );
};

export default Base;
