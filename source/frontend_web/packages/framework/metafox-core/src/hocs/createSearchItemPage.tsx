import { useGlobal } from '@metafox/framework';
import { Page } from '@metafox/layout';
import { createElement, useEffect } from 'react';
import { useResourceAction } from '../hooks';
import { PageCreatorConfig, PageParams } from '../types';

interface Params extends PageParams {
  tab: string;
  view: string;
  sort: string;
  when: string;
  search: string;
  category?: string[];
  defaultTab?: string;
  appName: string;
  hashtag: string;
}
interface Config<T> extends PageCreatorConfig<T> {
  readonly categoryName?: string;
  filterValuesCreator?: () => Partial<T>;
  viewResource?: string;
  apiParamsResourceDefault?: boolean;
  breadcrumb?: boolean;
  headingResourceName?: string;
  headingResourceKey?: string;
  headingLabelMessage?: string;
  backPage?: boolean;
}

export default function createSearchItemPage<T extends Params = Params>({
  appName,
  pageName,
  resourceName,
  categoryName,
  defaultTab = 'all',
  loginRequired = false,
  viewResource = 'viewAll',
  apiParamsResourceDefault = false,
  headingResourceName,
  headingResourceKey,
  headingLabelMessage,
  paramCreator,
  breadcrumb = true,
  backPage = true,
  backPageProps
}: Config<T>) {
  function BrowsePage(props: any) {
    const {
      createPageParams,
      i18n,
      jsxBackend,
      createContentParams,
      dispatch
    } = useGlobal();

    const config = useResourceAction(appName, resourceName, viewResource);
    const Title = jsxBackend.get('CategoryTitle');
    const SearchPageTitle = jsxBackend.get('SearchPageTitle');
    const pageParams = createPageParams<T>(
      props,
      prev => ({
        tab: prev.tab ?? defaultTab,
        appName,
        resourceName,
        breadcrumb,
        heading: i18n.formatMessage({ id: 'search_results' }),
        pageMetaName: `${appName}.${resourceName}`,
        backPage,
        backPageProps,
        _pageType: 'browseItem'
      }),
      paramCreator,
      prev => {
        const alt = i18n.formatMessage({ id: 'search_results' });

        if (categoryName) {
          const category = categoryName
            .split(',')
            .map(s => s.trim())
            .filter(Boolean)
            .find(x => prev[x]);

          if (category) {
            return {
              heading: createElement(Title, {
                identity: `${appName}.entities.${category}.${prev[category]}`,
                alt
              })
            };
          }
        } else if (headingResourceName) {
          const headingKey = headingResourceKey || headingResourceName;

          if (prev[headingKey]) {
            return {
              heading: createElement(SearchPageTitle, {
                identity: `${appName}.entities.${headingResourceName}.${prev[headingKey]}`,
                alt,
                message: headingLabelMessage
              })
            };
          }
        }

        return {
          heading: prev.heading || alt
        };
      }
    );

    console.log(pageParams);

    const contentParams = createContentParams({
      mainListing: {
        canLoadMore: true,
        contentType: resourceName,
        title: pageParams.heading,
        dataSource: {
          apiUrl: config?.apiUrl,
          apiRules: config?.apiRules,
          apiParams: apiParamsResourceDefault
            ? { ...config?.apiParams, ...pageParams }
            : pageParams
        }
      }
    });

    useEffect(() => {
      dispatch({ type: `renderPage/${pageName}`, payload: pageParams });
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pageParams]);

    return createElement(Page, {
      pageName,
      pageParams,
      contentParams,
      loginRequired
    });
  }

  return BrowsePage;
}
