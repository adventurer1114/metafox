/**
 * @type: ui
 * name: feedPage.view.list.embedItem
 */
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
  maxLinesTitle?: number;
  maxLinesDescription?: number;
  highlightSubInfo?: string;
  variant?: 'grid' | 'list';
  category?: string;
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
        overflow: 'hidden',
        padding: theme.spacing(3)
      },
      grid: {
        '& $itemOuter': {
          flexDirection: 'column',
          '$ $media': {
            width: '100%'
          }
        }
      },
      list: {
        '& $itemOuter': {
          flexDirection: 'row'
        },
        '& $wrapperInfoFlag': {
          marginTop: 'auto'
        },
        '& $media': {
          marginRight: theme.spacing(2)
        }
      },
      media: {
        width: '120px'
      },
      title: {
        '& a': {
          color: theme.palette.text.primary
        }
      },
      statistic: {
        display: 'flex',
        flexFlow: 'wrap',
        color: theme.palette.text.secondary
      },
      itemInner: {
        flex: 1,
        minWidth: 0,
        display: 'flex',
        flexDirection: 'column'
      },
      category: {
        '&:before': {
          content: '"."',
          margin: '0 4px'
        }
      },
      price: {},
      flagWrapper: {
        marginLeft: 'auto'
      },
      highlightSubInfo: {
        textTransform: 'uppercase'
      },
      btn: {
        marginRight: theme.spacing(1)
      },
      wrapperInfoFlag: {
        display: 'block'
      }
    }),
  { name: 'MuiFeedEmbedPageListt' }
);

const FeedEmbedPageListt = (props: Props) => {
  const {
    image,
    title,
    maxLinesTitle = 1,
    link,
    mediaRatio = '11',
    statistic,
    displayStatistic = 'total_view',
    is_featured,
    variant = 'list',
    category
  } = props;

  const classes = useStyles({ mediaRatio });
  const { i18n } = useGlobal();

  return (
    <div className={clsx(classes.item, classes[variant])}>
      <div className={classes.itemOuter}>
        {image && (
          <div className={classes.media}>
            <Image
              shape={'circle'}
              link={link}
              src={image}
              aspectRatio={mediaRatio}
            />
          </div>
        )}
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
            <Box className={classes.title}>
              <TruncateText variant="h4" lines={2} sx={{ mb: 1 }}>
                {title}
              </TruncateText>
            </Box>
          )}
          <Box className={classes.statistic}>
            <Statistic values={statistic} display={displayStatistic} />
            {category && <span className={classes.category}>{category}</span>}
          </Box>
          <Box
            className={classes.wrapperInfoFlag}
            display="flex"
            justifyContent="space-between"
            alignItems="flex-end"
            mt={2.5}
          >
            <Box display="flex" alignItems="center">
              <Button
                size="small"
                variant="outlined"
                color="primary"
                startIcon={<LineIcon icon={'ico-thumbup-o'} />}
              >
                {i18n.formatMessage({ id: 'like' })}
              </Button>
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

export default FeedEmbedPageListt;
