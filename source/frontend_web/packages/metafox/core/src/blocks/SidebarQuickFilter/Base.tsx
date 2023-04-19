import {
  BlockViewProps,
  useGlobal,
  useResourceAction,
  useResourceForm
} from '@metafox/framework';
import { FormBuilder } from '@metafox/form';
import { Block, BlockContent } from '@metafox/layout';
import { whenParamRules } from '@metafox/utils';
import { isEqual } from 'lodash';
import qs from 'querystring';
import React, { useState } from 'react';

export interface Props extends BlockViewProps {
  resourceNameAction?: string;
  formName?: string;
}

export default function SidebarQuickFilter({
  formName = 'search',
  resourceNameAction
}: Props) {
  const { usePageParams, compactUrl, useContentParams, navigate } = useGlobal();
  const pageParams = usePageParams();
  const contentParams = useContentParams();
  const { appName, resourceName, id } = pageParams;

  const config = useResourceAction(
    appName,
    resourceName,
    resourceNameAction || 'viewAll'
  );

  const formSchema = useResourceForm(appName, resourceName, formName);
  const [currentValue, setCurrentValue] = useState();

  const action = formSchema?.action;

  const onSubmit = (values, actions) => {
    actions.setSubmitting(false);
  };

  const onChange = ({ values }: any) => {
    if (isEqual(values, currentValue)) {
      return;
    }

    setCurrentValue(values);

    const apiRules =
      contentParams?.mainListing?.dataSource?.apiRules || config.apiRules;

    const params = whenParamRules(values, apiRules);
    const url = compactUrl(action, { id });

    navigate(`${url}?${qs.stringify(params)}`, { replace: true });
  };

  return (
    <Block testid="blockSearch">
      <BlockContent>
        <FormBuilder
          noHeader
          noBreadcrumb
          formSchema={formSchema}
          onSubmit={onSubmit}
          onChange={onChange}
        />
      </BlockContent>
    </Block>
  );
}
