// hooks
import { useGlobal } from '@metafox/framework';
import { SmartFormBuilder } from '@metafox/form';
import { AccountSettings } from '@metafox/user';
// components
import { Button, Skeleton } from '@mui/material';
import React, { useState } from 'react';
// actions
import { RELOAD_ACCOUNT } from '../../actions/accountSettings';
import LoadingComponent from './LoadingComponent';
import useStyles from './styles';

type EditablePrimaryLanguageProps = {
  loaded: boolean;
  title: string;
  data: AccountSettings;
};

export default function EditablePrimaryLanguage({
  loaded,
  title,
  data
}: EditablePrimaryLanguageProps) {
  const classes = useStyles();
  const { i18n } = useGlobal();

  const [isEdit, setEdit] = useState(false);

  return (
    <div className={classes.item} data-testid="editLanguage">
      <div className={classes.itemText}>
        <div className={classes.itemTitle}>{title}</div>
        <div className={classes.itemContent}>
          {isEdit ? (
            <SmartFormBuilder
              successAction={RELOAD_ACCOUNT}
              dataSource={{ apiUrl: '/user/account/language-form' }}
              loadingComponent={<LoadingComponent />}
              onSuccess={() => setEdit(false)}
              onCancel={() => setEdit(false)}
            />
          ) : (
            <div>
              {loaded ? (
                data?.language_name
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
