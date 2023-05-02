import React from 'react';
import { Block, BlockContent } from '@metafox/layout';
import { useGlobal, useResourceAction } from '@metafox/framework';
import {
  APP_NAME,
  LIMIT_SITE_BLOCK_DEFAULT
} from '@metafox/advertise/constants';
import { compactData } from '@metafox/utils';

export interface Props {
  [key: string]: any;
}

function Base(props: Props) {
  const { slotName = 'main', placement_id, blockLayout } = props;
  const { jsxBackend, getSetting } = useGlobal();

  const setting: any = getSetting('advertise');

  const displayLimit = ['main', 'top'].includes(slotName)
    ? LIMIT_SITE_BLOCK_DEFAULT
    : setting?.maximum_number_of_advertises_on_side_block;

  const ListView = jsxBackend.get('core.block.listview');

  const dataSource = useResourceAction(APP_NAME, APP_NAME, 'showAll');

  return (
    <Block testid="advertiseBlock">
      <BlockContent>
        <ListView
          displayLimit={displayLimit}
          dataSource={{
            apiMethod: dataSource.apiMethod || 'get',
            apiUrl: dataSource.apiUrl,
            apiParams: compactData(dataSource.apiParams, {
              placement_id,
              location: slotName
            })
          }}
          preventRefreshWhenEmpty
          blockLayout={blockLayout}
          itemLayout="Advertise List Item"
          gridLayout="Advertise List Item"
          itemProps={{ slotName }}
          emptyPage="hide"
          errorPage="hide"
          itemView="advertise.block.advertisesBlockItem"
        />
      </BlockContent>
    </Block>
  );
}

export default Base;
