import FormBuilder from '@metafox/form/FormBuilder';
import {
  BlockViewProps,
  useGlobal,
  useResourceAction,
  useResourceForm
} from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { TypeAll } from '@metafox/saved/constant';
import { whenParamRules } from '@metafox/utils';
import { isEmpty, isEqual } from 'lodash';
import qs from 'query-string';
import React from 'react';

export interface Props extends BlockViewProps {
  formName?: string;
}

export default function SidebarTypeFilter({ formName = 'sidebar' }: Props) {
  const { usePageParams, compactUrl, useContentParams, navigate } = useGlobal();
  const pageParams = usePageParams();
  const contentParams = useContentParams();
  const { appName, resourceName, type, collection_id } = pageParams;

  const config = useResourceAction(appName, resourceName, 'viewAll');
  const formSchema = useResourceForm(appName, resourceName, formName);
  const [currentValue, setCurrentValue] = React.useState();

  const action = collection_id
    ? `/saved/list/${collection_id}`
    : formSchema?.action;

  const isCheckChange = values => {
    let result = true;

    if (collection_id) {
      result = false;
    }

    if (!collection_id && values?.type === TypeAll) {
      result = true;
    } else {
      result = false;
    }

    return result;
  };

  const onSubmit = () => {};

  const onChange = ({ values }: any) => {
    if (
      isCheckChange(values) ||
      isEmpty(values) ||
      isEqual(values, currentValue)
    ) {
      return;
    }

    setCurrentValue(values);

    const apiRules =
      contentParams?.mainListing?.dataSource?.apiRules || config.apiRules;

    const params = whenParamRules(values, apiRules);
    const url = compactUrl(action, { type });

    navigate(`${url}?${qs.stringify(params)}`, { replace: true });
  };

  return (
    <Block testid="blockTypeFilter">
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
