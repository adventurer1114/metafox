import { useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { styled } from '@mui/material';
import { isString } from 'lodash';
import * as React from 'react';
import HeadlineSpan from './HeadlineSpan';
import ProfileLink from './FeedItemView/ProfileLink';
import useComposerContext from '../hooks/useComposerContext';

export const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1.5)
  })
);

export const HeadlineInfo = ({ info, item, embed_object, classes }) => {
  const { i18n, usePageParams, useGetItem, useSession } = useGlobal();
  const composerContext = useComposerContext();

  const pageParams = usePageParams();
  const { user: authUser } = useSession();

  const parent_user = useGetItem(item?.parent_user);
  const user = useGetItem(item?.user);
  const relationWithUser = useGetItem(embed_object?.relation_with);

  const isAuthUser = Number(pageParams?.profile_id) === Number(authUser?.id);
  const isCreator = Number(user?.id) === Number(pageParams?.profile_id);
  const isParentProfile =
    pageParams?.profile_id &&
    Number(pageParams?.profile_id) === Number(parent_user?.id);

  const compactParams = Object.assign(
    {},
    pageParams,
    composerContext?.pageParams
  );
  const values = {
    ...compactParams,
    friend_add: () => (
      <ProfileLink user={embed_object} className={classes.profileLink} />
    ),
    profile: () => (
      <HeadlineSpan>
        <ProfileLink user={parent_user} className={classes.profileLink} />
      </HeadlineSpan>
    ),
    relation_with: () => (
      <ProfileLink user={relationWithUser} className={classes.profileLink} />
    ),
    isOwnerTagged: item?.is_owner_tagged ? 1 : 0,
    isAuthUser: isAuthUser ? 1 : 0,
    isCreator: isCreator ? 1 : 0,
    parentType: parent_user?.module_name,
    currentGender: embed_object?.gender,
    isParentProfile: isParentProfile ? 1 : 0,
    hasRelationWith:
      item?.type_id === 'user_update_relationship' &&
      !!embed_object?.relation_with
        ? 1
        : 0
  };

  const infoHeadline =
    isString(info) && info
      ? i18n.formatMessage(
          {
            id: info
          },
          values
        )
      : info;

  if (!infoHeadline) return null;

  if (isString(infoHeadline))
    return (
      <HeadlineSpan>
        <HtmlViewer html={infoHeadline} />
      </HeadlineSpan>
    );

  return <HeadlineSpan>{infoHeadline}</HeadlineSpan>;
};

export default HeadlineInfo;
