import { Link, useGlobal } from '@metafox/framework';
import {
  Flag,
  Image,
  ImageRatio,
  ItemShape,
  LineIcon,
  Statistic,
  TruncateText
} from '@metafox/ui';
import { Box, Button, Theme } from '@mui/material';
import { createStyles, makeStyles } from '@mui/styles';
import clsx from 'clsx';
import * as React from 'react';

type Props = {
  title?: string;
  description?: string;
  link?: string;
  image?: string;
  mediaRatio?: ImageRatio;
  displayStatistic?: string;
  maxLinesTitle?: 1 | 2 | 3;
  maxLinesDescription?: 1 | 2 | 3;
  highlightSubInfo?: string;
  variant?: 'grid' | 'list';
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
      grid: {
        '& $itemOuter': {
          flexDirection: 'column'
        },
        '& $media': {
          width: '100%',
          height: '200px'
        }
      },
      list: {
        '& $itemOuter': {
          flexDirection: 'row',
          '& $media': {
            width: '200px'
          }
        },
        '& $wrapperInfoFlag': {
          marginTop: 'auto'
        }
      },
      media: {},
      title: {
        '& a': {
          color: theme.palette.text.primary
        }
      },
      description: {
        color: theme.palette.text.secondary,
        '& p': {
          margin: 0
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
      price: {
        fontWeight: theme.typography.fontWeightBold,
        color: theme.palette.warning.main
      },
      flagWrapper: {
        marginLeft: 'auto'
      },
      highlightSubInfo: {
        textTransform: 'uppercase'
      },
      actions: {
        marginRight: theme.spacing(1.5)
      },
      wrapperInfoFlag: {}
    }),
  { name: 'MuiFeedEventTemplate' }
);

const FeedEventTemplate = (props: Props) => {
  const {
    image,
    title,
    maxLinesTitle = 1,
    description,
    maxLinesDescription = 2,
    link,
    mediaRatio = '11',
    statistic,
    displayStatistic = 'total_view',
    is_featured,
    highlightSubInfo,
    variant = 'list'
  } = props;

  const classes = useStyles({ mediaRatio });
  const { i18n } = useGlobal();

  return (
    <div className={clsx(classes.item, classes[variant])}>
      <div className={classes.itemOuter}>
        {image && (
          <div className={classes.media}>
            <Image link={link} src={image} aspectRatio={mediaRatio} />
          </div>
        )}
        <div className={classes.itemInner}>
          {link ? (
            <Box mb={1} fontWeight={600} className={classes.title}>
              <Link to={link}>
                <TruncateText variant="h4" lines={maxLinesTitle}>
                  {title}
                </TruncateText>
              </Link>
            </Box>
          ) : (
            <Box className={classes.title} mb={1} fontWeight={600}>
              <TruncateText variant="h4" lines={maxLinesTitle}>
                {title}
              </TruncateText>
            </Box>
          )}
          {highlightSubInfo && (
            <Box
              className={classes.highlightSubInfo}
              mb={1}
              color={'primary.main'}
            >
              {highlightSubInfo}
            </Box>
          )}
          <Box className={classes.description}>
            <TruncateText variant={'body1'} lines={maxLinesDescription}>
              <div dangerouslySetInnerHTML={{ __html: description }} />
            </TruncateText>
          </Box>
          <Box
            className={classes.wrapperInfoFlag}
            display="flex"
            justifyContent="space-between"
            alignItems="flex-end"
            mt={2.5}
          >
            <Box display="flex" alignItems="center">
              <div className={classes.actions}>
                <Button
                  size="small"
                  variant="outlined"
                  color="primary"
                  startIcon={<LineIcon icon={'ico-calendar-star-o'} />}
                >
                  {i18n.formatMessage({ id: 'interested' })}
                </Button>
              </div>
              <Statistic
                values={statistic}
                display={displayStatistic}
                fontStyle={'minor'}
              />
            </Box>
            <div className={classes.flagWrapper}>
              {is_featured && <Flag type={'is_featured'} />}
            </div>
          </Box>
        </div>
      </div>
    </div>
  );
};

export default FeedEventTemplate;
