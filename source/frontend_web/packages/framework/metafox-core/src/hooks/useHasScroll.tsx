import useScrollRef from '@metafox/layout/useScrollRef';
import React from 'react';

function getDocHeight() {
  const x = document;

  return Math.max(
    x.body.scrollHeight,
    x.documentElement.scrollHeight,
    x.body.offsetHeight,
    x.documentElement.offsetHeight,
    x.body.clientHeight,
    x.documentElement.clientHeight
  );
}

export default function useHasScroll(init) {
  const scrollRef = useScrollRef();
  const [hasSCroll, setHasScroll] = React.useState(init);
  const node = scrollRef.current;

  const checkSCrollExist = React.useCallback(() => {
    if (node) {
      setHasScroll(node.scrollHeight > node.clientHeight);
    } else {
      setHasScroll(getDocHeight() > window.innerHeight);
    }
  }, [node]);

  React.useEffect(() => {
    checkSCrollExist();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return [hasSCroll, checkSCrollExist];
}
