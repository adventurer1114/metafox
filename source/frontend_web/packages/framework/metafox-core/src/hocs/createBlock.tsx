import { useGlobal } from '@metafox/framework';
import { BlockContext } from '@metafox/layout';
import { filterShowWhen, mergeObjectProps } from '@metafox/utils';
import { isString, merge } from 'lodash';
import React from 'react';
import { BlockViewProps, CreateBlockParams } from '../types';

export default function createBlock<T extends BlockViewProps = BlockViewProps>({
  name,
  extendBlock,
  overrides = {},
  defaults = {},
  custom
}: CreateBlockParams<T>) {
  const ConnectedBlock = (newProps: T) => {
    const {
      jsxBackend,
      usePreference,
      useLoggedIn,
      usePageParams,
      i18n,
      useGetItem,
      getSetting,
      layoutBackend,
      getAcl
    } = useGlobal();
    const { themeId } = usePreference();
    const loggedIn = useLoggedIn();
    const pageParams = usePageParams();
    const setting = getSetting();
    const acl = getAcl() as Object;

    const { identity } = pageParams;
    const profile = useGetItem(identity);

    const BaseBlock = isString(extendBlock)
      ? jsxBackend.get(extendBlock)
      : extendBlock;

    let mergedProps = mergeObjectProps<T>(
      { themeId },
      {},
      defaults,
      newProps,
      undefined,
      overrides
    ) as T;

    const styled = layoutBackend.normalizeDisplayingPresets(mergedProps);

    mergedProps = merge(mergedProps, styled);

    const show = filterShowWhen([mergedProps], {
      profile,
      pageParams,
      setting,
      acl
    }).length;

    // hide Profile-Tab Contained when want show detail album in user profile
    if (pageParams.profile_page && pageParams.album_id && !show) {
      return null;
    }

    // authRequired
    if (mergedProps.authRequired && !loggedIn) {
      return null;
    }

    if (!show && mergedProps.privacyEmptyPage) {
      const {
        title = 'content_private',
        description = 'content_private_description',
        ...others
      } = Object.assign({}, mergedProps.privacyEmptyPageProps);
      const PrivacyEmptyPage = jsxBackend.get(mergedProps.privacyEmptyPage);

      return (
        <PrivacyEmptyPage
          title={i18n.formatMessage({ id: title })}
          description={i18n.formatMessage({ id: description })}
          {...others}
        />
      );
    }

    if (!show) {
      return null;
    }

    mergedProps.compose = fn => fn(mergedProps as any);

    return React.createElement(
      BlockContext.Provider,
      { value: mergedProps },
      React.createElement(BaseBlock, mergedProps as any)
    );
  };

  ConnectedBlock.editorConfig = {
    extendBlock,
    overrides,
    defaults,
    custom
  };

  const baseName = isString(extendBlock)
    ? extendBlock
    : extendBlock.displayName;

  ConnectedBlock.displayName = `createBlock(${name ? name : baseName})`;

  return ConnectedBlock;
}
