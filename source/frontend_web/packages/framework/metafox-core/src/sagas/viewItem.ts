import getGlobalContext from './getGlobalContext';
import getResourceAction from './getResourceAction';

export default function* viewItem(
  appName: string,
  resourceName: string,
  id: string
) {
  const { navigate, compactUrl } = yield* getGlobalContext();

  const action = yield* getResourceAction(appName, resourceName, 'viewItem');

  if (!action?.pageUrl) return;

  const url = compactUrl(action.pageUrl, { id });

  navigate(url);
}
