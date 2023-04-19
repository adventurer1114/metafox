/**
 * @type: block
 * name: user.block.mfa
 */

import { RemoteFormBuilder } from '@metafox/form';
import {
  BlockViewProps,
  createBlock,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { MULTI_FACTOR_AUTH_TOKEN } from '@metafox/user/constant';
import React from 'react';

function MfaBlock({ title }) {
  const { localStore, jsxBackend } = useGlobal();
  const dataSource = useResourceAction('mfa', 'user_auth', 'authForm');

  const mfa_token = localStore.get(MULTI_FACTOR_AUTH_TOKEN);
  const EmptyPage = jsxBackend.get('core.block.error404');

  React.useEffect(() => {
    return () => {
      localStore.remove(MULTI_FACTOR_AUTH_TOKEN);
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  if (!mfa_token || !dataSource) return React.createElement(EmptyPage);

  return (
    <Block testid="blockMFA">
      <BlockContent>
        <RemoteFormBuilder
          dataSource={{
            ...dataSource,
            apiParams: { ...dataSource.apiParams, mfa_token }
          }}
          navigationConfirmWhenDirty={false}
        />
      </BlockContent>
    </Block>
  );
}

export default createBlock<BlockViewProps>({
  extendBlock: MfaBlock,
  defaults: {
    title: 'authentication',
    blockLayout: 'Resend Email - Contained'
  }
});
