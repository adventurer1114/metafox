import { useLocation } from 'react-router';
import { useGlobal } from '../hooks';
import React from 'react';
import { LOAD_PAGE_META } from '../constants';

const SeoDataAware = () => {
  const { state, pathname: _pathname } = useLocation();
  const { dispatch } = useGlobal();
  const pathname = state?.as || _pathname;

  React.useEffect(
    () => {
      dispatch({
        type: LOAD_PAGE_META,
        payload: { pathname }
      });
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [pathname]
  );

  return null;
};

export default SeoDataAware;
