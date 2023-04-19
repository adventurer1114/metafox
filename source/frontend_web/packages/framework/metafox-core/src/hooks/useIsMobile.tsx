/**
 * @type: service
 * name: useIsMobile
 */
import { useGlobal } from '@metafox/framework';

const useIsMobile = () => {
  const { useLayoutPageSize } = useGlobal();
  const pageSize = useLayoutPageSize();
  const isSmallSize = pageSize.indexOf('small') !== -1;

  return window.screen.width < window.screen.height || isSmallSize;
};

export default useIsMobile;
