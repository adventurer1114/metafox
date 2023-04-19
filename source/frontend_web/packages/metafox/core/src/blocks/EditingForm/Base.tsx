import { BlockViewProps, useGlobal, useResourceActions } from '@metafox/framework';
import { SmartFormBuilder } from '@metafox/form';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import React from 'react';

export interface EditingFormProps extends BlockViewProps {
  initialValues: any;
  formName: string;
  appName: string;
  resourceName: string;
  formSchema?: any;
}

export default function EditingForm({
  title,
  formName,
  appName,
  resourceName,
  formSchema,
  initialValues,
  blockProps
}: EditingFormProps) {
  const { dispatch, usePageParams } = useGlobal();
  const config = useResourceActions(appName, resourceName);
  const pageParams = usePageParams();
  const apiUrl = config[formName].apiUrl;
  const dataSource = { apiUrl, apiParams: { id: pageParams.id } };

  // Update redux store.
  const onChange = React.useCallback(
    (data: Record<string, any>) => {
      dispatch({
        type: 'formValues/onChange',
        payload: {
          formName,
          data
        }
      });
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  return (
    <Block>
      <BlockHeader title={title} />
      <BlockContent>
        <SmartFormBuilder
          onChange={onChange}
          initialValues={initialValues}
          formSchema={formSchema}
          dataSource={dataSource}
        />
      </BlockContent>
    </Block>
  );
}
