import { useGlobal, Link } from '@metafox/framework';
import React from 'react';
import { SettingBlockProps as Props } from './common';
import useStyles from './styles';
import { Skeleton } from '@mui/material';

export default function EditablePreferredCurrency({
  loaded,
  title,
  data
}: Props) {
  const classes = useStyles();
  const { i18n } = useGlobal();
  const { extra } = data || {};

  if (!extra?.can_delete_account && loaded) return null;

  return (
    <div className={classes.item} data-testid="cancelAccount">
      {loaded ? (
        <Link to={'/user/remove/'} color={'error.main'}>
          {i18n.formatMessage({ id: 'cancel_account' })}
        </Link>
      ) : (
        <Skeleton variant="text" width={100} />
      )}
    </div>
  );
}
