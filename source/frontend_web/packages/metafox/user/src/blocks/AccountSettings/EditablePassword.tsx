/* eslint-disable quotes */
// hooks
import { useGlobal } from '@metafox/framework';
import { SmartFormBuilder } from '@metafox/form';
import { RELOAD_ACCOUNT } from '@metafox/user/actions/accountSettings';
import { Button, Skeleton } from '@mui/material';
// components
// styles
import React, { useState } from 'react';
import { SettingBlockProps as Props } from './common';
import LoadingComponent from './LoadingComponent';
import useStyles from './styles';

export default function EditablePassword({ title, loaded }: Props) {
  const classes = useStyles();
  const { i18n } = useGlobal();

  const [isEdit, setEdit] = useState(false);

  return (
    <div className={classes.item} data-testid="editPassword">
      <div className={classes.itemText}>
        <div className={classes.itemTitle}>{title}</div>
        <div className={classes.itemContent}>
          {isEdit ? (
            <SmartFormBuilder
              successAction={RELOAD_ACCOUNT}
              dataSource={{ apiUrl: '/user/account/password-form' }}
              loadingComponent={<LoadingComponent />}
              onSuccess={() => setEdit(false)}
              onCancel={() => setEdit(false)}
            />
          ) : (
            <div>
              {loaded ? (
                '**************'
              ) : (
                <Skeleton variant="text" width={100} />
              )}
            </div>
          )}
        </div>
      </div>
      <div className={classes.itemButton}>
        {!isEdit && (
          <Button
            data-testid="buttonEdit"
            disabled={!loaded}
            className={classes.btnEdit}
            size={'medium'}
            variant={'outlined'}
            color={'primary'}
            onClick={() => setEdit(true)}
          >
            {i18n.formatMessage({ id: 'edit' })}
          </Button>
        )}
      </div>
    </div>
  );
}
