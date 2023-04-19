/**
 * @type: block
 * name: admincp.block.controlled
 * bundle: admincp
 */
import React from 'react';
import { createBlock, useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { getErrString } from '@metafox/utils';
import { Alert, CircularProgress, Typography } from '@mui/material';
import { Box } from '@mui/system';

/**
 * api return block(s) component and props for handle jsx render
 * @param param0
 * @returns
 */
export const AdminControlledBlock = ({ title, dataSource: _dataSource }) => {
  const { useFetchDetail, jsxBackend, usePageParams } = useGlobal();
  const pageParams = usePageParams();

  const dataSource = _dataSource ?? pageParams.dataSource;

  const [data, loading, error] = useFetchDetail<{
    title?: string;
    description?: string;
    component?: string;
    props?: object;
  }>({
    dataSource
  });

  return (
    <Block>
      <BlockHeader title={title || data?.title} />
      <BlockContent>
        {data?.description ? (
          <Typography paragraph>
            <HtmlViewer html={data.description} />
          </Typography>
        ) : null}
        {loading ? (
          <Box>
            <CircularProgress variant="indeterminate" />
          </Box>
        ) : null}
        {error ? (
          <Box>
            <Alert severity="error">{getErrString(error)}</Alert>
          </Box>
        ) : null}
        {!loading && !error && data?.component
          ? jsxBackend.render({
              component: data.component,
              props: data.props
            })
          : null}
      </BlockContent>
    </Block>
  );
};

export default createBlock({
  extendBlock: AdminControlledBlock,
  defaults: {},
  overrides: { noHeader: false }
});
