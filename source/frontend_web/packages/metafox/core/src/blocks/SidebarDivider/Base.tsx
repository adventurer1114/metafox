import { BlockViewProps, useGlobal } from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { Divider } from '@mui/material';
import * as React from 'react';

export interface Props extends BlockViewProps {
  dividerVariant: 'middle' | 'inset';
}

export default function SidebarDivider({ dividerVariant, blockProps }: Props) {
  const { usePreference, useTheme } = useGlobal();
  const { themeId } = usePreference();
  const theme = useTheme();

  return (
    <Block>
      <BlockContent>
        <Divider
          variant={dividerVariant ?? 'inset'}
          sx={{
            borderColor:
              themeId === 'default' ? theme.palette.border?.secondary : '#e0e0e0'
          }}
        />
      </BlockContent>
    </Block>
  );
}
