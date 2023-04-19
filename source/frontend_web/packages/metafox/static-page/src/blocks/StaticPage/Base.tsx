import { useGlobal } from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { UIBlockViewProps } from '@metafox/ui';
import { Box, styled } from '@mui/material';
import React from 'react';

export interface Props extends UIBlockViewProps {}

const BoxWrapper = styled(Box, { name: 'BoxWrapper' })(({ theme }) => ({
  margin: theme.spacing(2),
  backgroundColor: theme.palette.background.paper,
  padding: theme.spacing(2),
  borderRadius: theme.spacing(0.75)
}));

export default function Policy({ title, blockProps }: Props) {
  const { useSubject, usePageParams } = useGlobal();
  const params = usePageParams();

  const data = useSubject();

  console.log(params, data);

  return (
    <Block>
      <BlockContent>
        <BoxWrapper>
          <div dangerouslySetInnerHTML={{ __html: data?.text }}></div>
        </BoxWrapper>
      </BlockContent>
    </Block>
  );
}
