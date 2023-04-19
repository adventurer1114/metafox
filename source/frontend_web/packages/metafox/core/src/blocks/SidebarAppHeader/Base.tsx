/**
 * @type: ui
 * name: core.sideAppHeaderBlock
 */
import {
  RouteLink,
  SideMenuBlockProps,
  useAppUI,
  useGlobal
} from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { Typography } from '@mui/material';
import * as React from 'react';
import useStyles from './styles';
import ChevronRightOutlinedIcon from '@mui/icons-material/ChevronRightOutlined';

const APP_GROUP = 'group';
const APP_PAGE = 'page';

export interface Props extends SideMenuBlockProps {
  sidebarHeaderName: string;
  titleProperty?: string;
}

export default function SideMenuBlock({
  blockProps,
  sidebarHeaderName = 'homepageHeader',
  titleProperty
}: Props) {
  const classes = useStyles();
  const { usePageParams, useFetchDetail, compactUrl, i18n } = useGlobal();
  const {
    appName,
    breadcrumb,
    heading,
    pageTitle,
    id,
    resourceName,
    backPage,
    backPageProps
  } = usePageParams();

  const sidebarHeader = useAppUI(appName, sidebarHeaderName);

  const [item] = useFetchDetail({
    dataSource:
      id && (resourceName === APP_PAGE || resourceName === APP_GROUP)
        ? { apiUrl: `/${appName}/${id}` }
        : null
  });

  if (!sidebarHeader) return null;

  const { title, to, subTitle, toSubTitle } = sidebarHeader;
  const customTitle = titleProperty && item && item[titleProperty];
  const link = customTitle && `/${resourceName}/${id}`;

  const backProps = backPageProps
    ? backPageProps
    : { title: pageTitle, to: resourceName };

  if (breadcrumb) {
    return (
      <Block testid="blockAppHeader">
        {subTitle && toSubTitle && item ? (
          <BlockContent className={classes.breadcrumbs}>
            <RouteLink
              to={link || compactUrl(to, item || {})}
              className={classes.link}
            >
              <Typography variant="body2" color="primary">
                {i18n.formatMessage({ id: title })}
              </Typography>
            </RouteLink>
            <ChevronRightOutlinedIcon fontSize="small" color="primary" />
            <RouteLink
              to={link || compactUrl(toSubTitle, item || {})}
              className={classes.link}
            >
              <Typography variant="body2" color="primary">
                {i18n.formatMessage({ id: subTitle })}
              </Typography>
            </RouteLink>
          </BlockContent>
        ) : (
          <BlockContent>
            {(item || backPage) && (
              <RouteLink
                to={link || compactUrl(to, item || {})}
                className={classes.link}
              >
                <Typography variant="body2" color="primary">
                  {i18n.formatMessage({
                    id: pageTitle ?? customTitle ?? title
                  })}
                </Typography>
              </RouteLink>
            )}
            {heading && (
              <div className={classes.header}>
                <Typography component="h1" variant="h3" color="textPrimary">
                  {typeof heading === 'string'
                    ? i18n.formatMessage({ id: heading })
                    : i18n.formatMessage({ id: title })}
                </Typography>
              </div>
            )}
          </BlockContent>
        )}
      </Block>
    );
  }

  return (
    <Block testid="blockAppHeader">
      <BlockContent>
        {backPage ? (
          <RouteLink to={backProps.to} className={classes.link}>
            <Typography variant="body2" color="primary">
              {i18n.formatMessage({ id: backProps.title })}
            </Typography>
          </RouteLink>
        ) : null}
        <div className={classes.header}>
          <Typography
            component="h1"
            variant="h3"
            color="textPrimary"
            className={classes.title}
          >
            {i18n.formatMessage({ id: pageTitle ?? title })}
          </Typography>
        </div>
      </BlockContent>
    </Block>
  );
}
