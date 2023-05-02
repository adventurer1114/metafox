/**
 * @type: ui
 * name: advertise.ui.inViewImpression
 */

import { useGlobal } from '@metafox/framework';
import { isEmpty } from 'lodash';
import React from 'react';
import { useInView } from 'react-intersection-observer';

const InViewImpression = ({ item }: any) => {
  const { dispatch } = useGlobal();

  const [refScrollInView, inView] = useInView({
    threshold: 0,
    triggerOnce: true
  });

  React.useEffect(() => {
    if (isEmpty(item)) return;

    if (inView) {
      dispatch({ type: 'advertise/updateImpression', payload: item });
    }

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [inView]);

  return <span ref={refScrollInView} />;
};

export default InViewImpression;
