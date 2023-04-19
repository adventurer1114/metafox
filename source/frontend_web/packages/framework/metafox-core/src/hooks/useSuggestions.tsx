import { uniqueId } from 'lodash';
import React from 'react';
import { useGlobal } from '..';

export default function useSuggestions<T = Record<string, any>>({
  apiUrl,
  type = '@suggestion',
  limit,
  initialParams
}: {
  apiUrl: string;
  isCached?: boolean;
  type?: string;
  limit?: number;
  initialParams?: Record<string, any>;
}): [
  {
    items: T[];
    loading: boolean;
    error?: undefined;
  },
  (query: string) => void
] {
  const mounted = React.useRef(true);
  const none = React.useState<string>(uniqueId('s'));

  const [data, setData] = React.useState<{
    items: T[];
    loading: boolean;
    error?: undefined;
  }>({
    items: [],
    loading: true
  });

  const { dispatch } = useGlobal();

  React.useEffect(() => {
    handleChange('');

    return () => {
      mounted.current = false;
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  const handleSuggestions = data => {
    if (mounted.current && Array.isArray(data.items)) setData(data);
  };

  const handleChange = (q: string) => {
    dispatch({
      type,
      payload: {
        q,
        apiUrl,
        none,
        limit,
        initialParams
      },
      meta: { onSuccess: handleSuggestions }
    });
  };

  return [data, handleChange];
}
