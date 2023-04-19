import {
  AppUIConfig,
  CategoryBlockProps,
  useGlobal,
  RouteLink as Link
} from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { Box } from '@mui/material';
import * as React from 'react';
import { LineIcon } from '@metafox/ui';

export type Props = {
  sidebarCategory: AppUIConfig['sidebarCategory'];
  appName?: string;
} & CategoryBlockProps;

export default function SideCategoryBlock({
  title,
  sidebarCategory,
  blockProps,
  appName: appNameProp
}: Props) {
  const { usePageMeta } = useGlobal();

  const data = usePageMeta();
  const { breadcrumbs = [] } = data || {};

  if (!breadcrumbs?.length) return null;

  return (
    <Block testid="blockBreadcrumb">
      <BlockContent>
        {breadcrumbs.map(item => (
          <Box
            component={'span'}
            sx={{
              display: 'inline-flex',
              alignItems: 'center',
              '&:last-child': { fontWeight: 700 }
            }}
            key={item?.label.toString()}
          >
            <Link to={item?.to}>{item?.label}</Link>
            <Box mx={1} component={'span'} color={'text.hint'}>
              <LineIcon sx={{ fontSize: '10px' }} icon="ico-angle-right" />
            </Box>
          </Box>
        ))}
      </BlockContent>
    </Block>
  );
}
