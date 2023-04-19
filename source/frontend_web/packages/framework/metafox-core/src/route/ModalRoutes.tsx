import { useGlobal } from '@metafox/framework';
import React from 'react';
import { useLocation, createPath } from 'react-router-dom';

export default function ModalRoutes() {
  const mn = useGlobal();
  const { routeBackend } = mn;
  const location = useLocation() as any;
  const url = createPath(location);
  let isModal = false;

  const [modal, setModal] = React.useState<{
    name: string;
    url: string;
    params: object;
    component: React.FC;
  }>();

  // const isFirst = !!modal;

  if (location.state?.asModal) {
    isModal = true;
  }

  // eslint-disable-next-line react-hooks/exhaustive-deps
  // test if can map
  React.useEffect(() => {
    if (isModal) routeBackend.getModal(url).then(setModal);

    if (!isModal) setModal(undefined);

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [url, isModal]);

  if (!isModal || !modal) return null;

  const { component: Page, params } = modal;

  return <Page {...params} />;
}
