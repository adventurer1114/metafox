import { BlockViewProps, useGlobal } from '@metafox/framework';
import { SmartFormBuilder } from '@metafox/form';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import React from 'react';
import useStyles from './styles';

export interface Props extends BlockViewProps {
  initialValues: any;
}

export default function Base({ title, initialValues, blockProps }: Props) {
  const { dispatch, useContentParams, usePageParams } = useGlobal();
  const classes = useStyles();
  const { mainForm } = useContentParams();
  const pageParams = usePageParams();

  // Update redux store.
  const onChange = React.useCallback(
    (data: Record<string, any>) => {
      if (!mainForm.formName) return;

      dispatch({
        type: 'formValues/onChange',
        payload: {
          formName: mainForm.formName,
          data
        }
      });
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  if (!mainForm?.dataSource && !mainForm.formSchema) {
    return null;
  }

  const {
    dataSource,
    noHeader,
    noBreadcrumb,
    formSchema,
    breadcrumbs,
    minHeight
  } = mainForm;

  return (
    <Block testid="mainForm">
      <BlockHeader title={title} />
      <BlockContent style={{ minHeight }}>
        <div className={classes.formWrapper}>
          <SmartFormBuilder
            noHeader={noHeader}
            breadcrumbs={breadcrumbs}
            noBreadcrumb={noBreadcrumb}
            formSchema={formSchema}
            pageParams={pageParams}
            dataSource={dataSource}
            onChange={onChange}
            initialValues={initialValues}
            changeEventName={mainForm.changeEventName}
          />
        </div>
      </BlockContent>
    </Block>
  );
}
