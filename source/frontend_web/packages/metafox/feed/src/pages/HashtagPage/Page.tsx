/**
 * @type: route
 * name: feed.hashtag
 * path: /hashtag/search, /hashtag/:q
 * chunkName: pages.feed
 * chunkName: pages.event
 * bundle: web
 */
import { createSearchItemPage } from '@metafox/framework';

export default createSearchItemPage({
  appName: 'search',
  resourceName: 'search',
  pageName: 'feed.hashtag',
  breadcrumb: false,
  backPage: true,
  backPageProps: {
    title: 'Home',
    to: '/'
  },
  paramCreator: prev => ({
    tag: undefined,
    tab: prev.view,
    view: prev.view,
    heading: `#${prev.q ? prev.q : 'Hashtag'}`,
    pageTitle: `#${prev.q ? prev.q : 'Hashtag'}`
  })
});
