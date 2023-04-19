import {
  PageCreatorConfig,
  PageParams,
  useAbortControl,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import React, { useEffect } from 'react';
import { fetchDetail } from '../actions';
interface Params extends PageParams {
  resourceName: string;
  identity: string;
}

interface Config<T> extends PageCreatorConfig<T> {
  readonly idName?: string;
  readonly resourceName: string;
  readonly component: string;
  dialogId?: string;
}

export default function createViewItemModal<T extends Params = Params>({
  appName,
  resourceName,
  component,
  pageName,
  idName = 'id',
  loginRequired = false,
  paramCreator,
  dialogId
}: Config<T>) {
  function ViewItemModal(props: any) {
    const { createPageParams, dispatch, dialogBackend, use } = useGlobal();
    const abortId = useAbortControl();
    const [error, setErr] = React.useState<number>(0);
    const [loading, setLoading] = React.useState<boolean>(true);
    const loadedDetail = props?.location?.state?.loadedDetail;

    const pageParams = createPageParams<Params>(props, prev => ({
      appName,
      resourceName,
      _pageType: 'viewItemInModal',
      identity: `${appName}.entities.${resourceName}.${prev[idName]}`
    }));

    const onFailure = React.useCallback((error: any) => {
      // eslint-disable-next-line no-console
      setErr(error);
      setLoading(false);
    }, []);

    const onSuccess = React.useCallback(() => {
      setLoading(false);
    }, []);

    useEffect(() => {
      use({ getPageParams: () => pageParams });
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [pageParams]);

    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const config = useResourceAction(appName, resourceName, 'viewItem');

    const { identity } = pageParams;

    const id = pageParams[idName];

    useEffect(() => {
      if (loadedDetail) return;

      dispatch(
        fetchDetail(config.apiUrl, { id }, onSuccess, onFailure, abortId)
      );
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [config.apiUrl, id]);

    useEffect(() => {
      if (loading) return;

      dialogBackend.present({
        component,
        props: { identity, error },
        dialogId
      });
      // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [identity, error, loading]);

    return null;
  }

  ViewItemModal.displayName = `createViewItemModal(${pageName})`;

  return ViewItemModal;
}
