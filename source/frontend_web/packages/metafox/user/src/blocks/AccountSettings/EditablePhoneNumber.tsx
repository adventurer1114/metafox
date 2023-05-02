// hooks
import { SmartFormBuilder } from '@metafox/form';
import { useGlobal, useResourceAction } from '@metafox/framework';
// actions
import { RELOAD_ACCOUNT } from '@metafox/user/actions/accountSettings';
import { APP_USER } from '@metafox/user/constant';
import { Button, Skeleton } from '@mui/material';
// styles
import React, { useState } from 'react';
import { SettingBlockProps as Props } from './common';
import LoadingComponent from './LoadingComponent';
import useStyles from './styles';

export default function EditablePhoneNumber({ loaded, title, data }: Props) {
  const classes = useStyles();
  const { i18n } = useGlobal();
  const dataSource = useResourceAction(
    APP_USER,
    APP_USER,
    'getPhoneNumberSettingForm'
  );

  const [isEdit, setEdit] = useState(false);

  return (
    <div className={classes.item} data-testid="editEmail">
      <div className={classes.itemText}>
        <div className={classes.itemTitle}>{title}</div>
        <div className={classes.itemContent}>
          {isEdit ? (
            <SmartFormBuilder
              successAction={RELOAD_ACCOUNT}
              dataSource={dataSource}
              loadingComponent={LoadingComponent}
              onSuccess={() => setEdit(false)}
              onCancel={() => setEdit(false)}
            />
          ) : (
            <div>
              {loaded ? (
                data?.phone_number
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
