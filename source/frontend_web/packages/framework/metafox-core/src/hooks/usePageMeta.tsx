/**
 * @type: service
 * name: usePageMeta
 */

import {
  getPageMetaDataSelector,
  GlobalState,
  useLocation
} from '@metafox/framework';
import useGlobal from './useGlobal';
import { useSelector } from 'react-redux';

export default function usePageMeta() {
  const { getSetting } = useGlobal();
  const { pathname } = useLocation();
  const data = useSelector((state: GlobalState) =>
    getPageMetaDataSelector(state, pathname)
  );

  const root = getSetting<{
    description: string;
    keywords: string;
    title: string;
    site_title: string;
  }>('core.general');

  return data ?? root;
}
