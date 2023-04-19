import { useGlobal } from '@metafox/framework';
import React from 'react';

export default function ProfileFeed(props) {
  const { jsxBackend } = useGlobal();
  const { item } = props;

  const ListView = jsxBackend.get('core.block.listview');

  if (item?.profile_settings?.profile_view_profile === false) return null;

  return <ListView {...props} />;
}
