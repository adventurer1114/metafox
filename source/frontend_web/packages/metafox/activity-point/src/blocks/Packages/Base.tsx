import { ActivityItemProps, APP_ACTIVITY } from '@metafox/activity-point';
import {
  BlockViewProps,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import { Box, styled } from '@mui/material';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import React from 'react';

export type Props = BlockViewProps & ActivityItemProps;

const PackagesStyled = styled(Box, { name: 'ContentWrapper' })(({ theme }) => ({
  padding: theme.spacing(4)
}));

export default function Base({
  title,
  emptyPage = 'core.block.no_results',
  actions,
  ...rest
}: Props) {
  const { i18n, ListView } = useGlobal();

  const dataSource = useResourceAction(
    APP_ACTIVITY,
    'activitypoint_package',
    'viewAll'
  );

  return (
    <Block testid="activityPointBlock">
      <BlockHeader title={title} />
      <BlockContent>
        <PackagesStyled>
          <ListView
            itemView="activitypoint.itemView.packageItem"
            dataSource={dataSource}
            emptyPage="core.block.no_content_with_icon"
            emptyPageProps={{
              title: i18n.formatMessage({ id: 'no_package_found' }),
              description: i18n.formatMessage({
                id: 'there_are_no_ready_package'
              }),
              image: 'ico-box'
            }}
            blockLayout="Large Main Lists"
            itemLayout="ActivityPoint - Packages"
            gridLayout="ActivityPoint - Packages"
          />
        </PackagesStyled>
      </BlockContent>
    </Block>
  );
}

Base.displayName = 'ActivityPoint_Packages';
