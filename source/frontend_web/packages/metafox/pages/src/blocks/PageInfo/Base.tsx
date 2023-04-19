import { BlockViewProps, useGlobal } from '@metafox/framework';
import { RemoteFormBuilder } from '@metafox/form';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { compactUrl } from '@metafox/utils';
import * as React from 'react';

export type Props = BlockViewProps & {
  identity: string;
  data?: {
    apiMethod?: 'get' | 'post' | 'put' | 'patch' | 'delete';
    apiUrl?: string;
  };
};

export default function PageInfo({ data, identity }: Props) {
  const { i18n } = useGlobal();

  if (!identity || !data) return null;

  const id = identity.split('.')[3];

  return (
    <Block>
      <BlockHeader title={i18n.formatMessage({ id: 'page_info' })} />
      <BlockContent>
        <RemoteFormBuilder
          dataSource={{
            apiMethod: data.apiMethod,
            apiUrl: compactUrl(data.apiUrl, { id })
          }}
        />
      </BlockContent>
    </Block>
  );
}
