import { BlockViewProps, useGlobal } from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { List } from '@mui/material';
import * as React from 'react';
import { AppState } from '../../types';
import ListNotification from '@metafox/user/components/ListNotification';

export type Props = BlockViewProps & AppState['notificationSettings'];

export default function GeneralSettings({ data, title, blockProps }: Props) {
  const { dispatch } = useGlobal();

  React.useEffect(() => {
    dispatch({ type: 'setting/notificationSettings/FETCH' });
  }, [dispatch]);

  return (
    <Block>
      <BlockHeader title={title} />
      <BlockContent>
        <List disablePadding>
        {data.map(item => (
          <ListNotification item={item} key={item.var_name} typeAction='notificationSettings'/>
        ))}
        </List>
        
      </BlockContent>
    </Block>
  );
}
