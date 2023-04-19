import { HistoryState, useGlobal } from '@metafox/framework';
import { useMemo } from 'react';
import { useLocation } from 'react-router-dom';

export default function PageParamAware() {
  const { use, usePageParams } = useGlobal();
  const { key } = useLocation<HistoryState>();
  const pageParams = usePageParams();

  useMemo(() => {
    use({ getPageParams: () => pageParams });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [pageParams, key]);

  return null;
}
