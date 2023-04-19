/**
 * @type: service
 * name: routeBackend
 */
import { Manager } from '@metafox/framework/Manager';
import { RouteBackendConfig, Result } from './types';
import { match as pathToReg, MatchFunction } from 'path-to-regexp';
import { get } from 'lodash';
import qs from 'query-string';

interface RouteConfig {
  path: string;
  name: string;
  component: React.FC;
}

interface RouteItem {
  component: React.FC;
  name: string;
  match: MatchFunction;
}

type Group = 'modal' | 'page';

class RouteBackend {
  readonly options: RouteBackendConfig;

  private cached: Record<string, string> = {};

  private pages: RouteItem[];
  private modals: RouteItem[];

  private manager: Manager;

  constructor(options: Partial<RouteBackendConfig>) {
    this.options = Object.assign(
      {
        cache: true,
        pageNotFound: '/page-not-found',
        apiUrl: '/core/url-to-route'
      },
      options
    );
  }

  private transform(group: string) {
    const { getConfig } = this.manager;

    return getConfig<RouteConfig[]>(group, []).map(
      ({ path, name, component }) => ({
        component,
        name,
        match: pathToReg(path)
      })
    );
  }

  public bootstrap(manager: Manager) {
    this.manager = manager;

    this.pages = this.transform('routes');

    this.modals = this.transform('modals');
  }

  public cancel() {
    // to do others.
  }

  private byGroup(group: Group) {
    switch (group) {
      case 'modal':
        return this.modals;
      default:
        return this.pages;
    }
  }

  private check(url: string, group: Group): string | false {
    const { pathname } = new URL(url, 'http://locahost');

    if (this.options.cache && this.cached[url]) {
      url = this.cached[url];
    }

    if (this.options.cache && this.cached[pathname]) {
      url = this.cached[pathname];
    }

    const item = this.byGroup(group).find(({ match }) => {
      return match(url) || match(pathname);
    });

    return item ? url : false;
  }

  public match(url: string, group: Group): Result {
    const { pathname, search } = new URL(url, 'http://locahost');

    let item = this.byGroup(group).find(({ match }) => {
      return match(url) || match(pathname);
    });

    if (!item) {
      item = this.byGroup(group).find(({ name }) => {
        return name === 'core.error404';
      });
    }

    const found = item.match(url) || item.match(pathname);
    const searchParams = qs.parse(search);
    const params = Object.assign(searchParams, found ? found.params : {});

    return {
      name: item.name,
      pathname: url,
      component: item.component,
      params
    };
  }

  public async findPath(
    url: string,
    group: Group,
    setLoading?: (loading: boolean) => void
  ): Promise<string> {
    const { cache: enableCache, apiUrl, pageNotFound } = this.options;
    const { apiClient } = this.manager;

    if (enableCache && this.cached[url]) {
      url = this.cached[url];
    }

    const found = this.check(url, group);

    if (found) {
      return new Promise(resolve => resolve(found));
    }

    if (setLoading) setLoading(true);

    return apiClient
      .get(apiUrl, { params: { url } })
      .then(res => {
        const returnUrl = get(res, 'data.data.path', pageNotFound);
        this.cached[url] = returnUrl;

        return returnUrl;
      })
      .catch(() => {
        return pageNotFound;
      })
      .finally(() => {
        if (setLoading) setLoading(false);
      });
  }

  public async getPage(url: string): Promise<Result> {
    const { setRouteLoading } = this.manager;

    const res = await this.findPath(url, 'page', setRouteLoading);

    return this.match(res, 'page');
  }

  public async getModal(url: string): Promise<Result> {
    const res = await this.findPath(url, 'modal');

    return this.match(res, 'modal');
  }
}

export default RouteBackend;
