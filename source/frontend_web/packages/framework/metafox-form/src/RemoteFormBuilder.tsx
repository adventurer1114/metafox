import { useGlobal } from '@metafox/framework';
import {
  FormBuilder,
  FormSchemaShape,
  RemoteFormBuilderProps
} from '@metafox/form';
import React, { useEffect, useState } from 'react';
import ErrorPage from '@metafox/core/pages/ErrorPage/Page';

export default function RemoteFormBuilder({
  dataSource,
  dialog,
  pageParams,
  onLoaded,
  onFailure,
  hideWhenError = false,
  loadingComponent: LoadingView,
  preventReload = false,
  ...rest
}: RemoteFormBuilderProps) {
  const { useFetchDetail, dialogBackend, jsxBackend } = useGlobal();
  const [formSchema, setFormSchema] = useState<FormSchemaShape>();
  const [key, setKey] = useState('');
  const DefaultLoading = jsxBackend.get('form.DefaultLoading');
  const DialogLoadingComponent = jsxBackend.get('form.DialogLoadingComponent');
  const [data, loading, error, , meta] = useFetchDetail({
    dataSource,
    pageParams,
    allowRiskParams: false,
    forceReload: true,
    preventReload,
    cacheKey: key
  });

  const onReset = () => {
    setKey(Math.random().toString());
  };

  useEffect(() => {
    if (!error) return;

    if (!dialog) {
      dialogBackend.dismiss();
    }

    typeof onFailure === 'function' && onFailure(error);

    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [error]);

  useEffect(() => {
    if (data) {
      setFormSchema(data);

      if (onLoaded) {
        onLoaded({ data, meta });
      }
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [data, pageParams]);

  if ((error && hideWhenError) || !dataSource) return null;

  const DefaultLoadingComponent = dialog
    ? DialogLoadingComponent
    : DefaultLoading;

  return (
    <ErrorPage
      loading={loading}
      error={error}
      loadingComponent={LoadingView || DefaultLoadingComponent}
    >
      <FormBuilder
        {...rest}
        onReset={onReset}
        dialog={dialog}
        formSchema={formSchema}
        pageParams={pageParams}
      />
    </ErrorPage>
  );
}
