import { BlockViewProps, useGlobal } from '@metafox/framework';
// layouts
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
// actions
import { FETCH_SETTING } from '@metafox/user/actions/accountSettings';
import { AppState } from '@metafox/user/types';
import React, { useEffect } from 'react';
// components
import EditableEmail from './EditableEmail';
import EditableFullName from './EditableFullname';
import EditablePassword from './EditablePassword';
import EditablePreferredCurrency from './EditablePreferredCurrency';
import EditablePrimaryLanguage from './EditablePrimaryLanguage';
import EditableUserName from './EditableUserName';
import CancelAccount from './CancelAccount';
// types
import useStyles from './styles';
import EditablePhoneNumber from './EditablePhoneNumber';

export type Props = BlockViewProps & AppState['accountSettings'];

const GeneralSettings = ({ data, loaded, title }: Props) => {
  const classes = useStyles();
  const { i18n, dispatch } = useGlobal();

  // refresh for new data anytime its mount
  useEffect(() => {
    dispatch({ type: FETCH_SETTING });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <Block testid="accountSettings">
      <BlockHeader title={title} />
      <BlockContent>
        <div className={classes.root}>
          <EditableFullName
            loaded={loaded}
            title={i18n.formatMessage({ id: 'full_name' })}
            data={data}
          />
          <EditableUserName
            loaded={loaded}
            title={i18n.formatMessage({ id: 'username' })}
            data={data}
          />
          <EditableEmail
            loaded={loaded}
            title={i18n.formatMessage({ id: 'email_address' })}
            data={data}
          />
          <EditablePhoneNumber
            loaded={loaded}
            title={i18n.formatMessage({ id: 'phone_number' })}
            data={data}
          />
          <EditablePassword
            loaded={loaded}
            title={i18n.formatMessage({ id: 'password' })}
            data={data}
          />
          <EditablePrimaryLanguage
            loaded={loaded}
            title={i18n.formatMessage({ id: 'primary_language' })}
            data={data}
          />
          <EditablePreferredCurrency
            loaded={loaded}
            title={i18n.formatMessage({ id: 'preferred_currency' })}
            data={data}
          />
          <CancelAccount loaded={loaded} data={data} />
        </div>
      </BlockContent>
    </Block>
  );
};

export default GeneralSettings;
