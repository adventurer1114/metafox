import { shouldShowTypePrivacy } from '@metafox/feed/utils';
import { BlockViewProps, useGetItem, useGlobal } from '@metafox/framework';
import { Block, BlockContent, BlockHeader } from '@metafox/layout';
import { UserAvatar } from '@metafox/ui';
import { UserItemShape } from '@metafox/user';
import { filterShowWhen } from '@metafox/utils';
import { Box, styled } from '@mui/material';
import { isEmpty } from 'lodash';
import * as React from 'react';
import composerConfig from '../../composerConfig';
import useStatusComposer from '../../hooks/useStatusComposer';
import Control from './Control';

export interface Props extends BlockViewProps {
  variant: 'default' | 'expanded';
  item: UserItemShape;
}

const strategy = 'block';

const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(({ theme }) => ({
  marginRight: theme.spacing(1.5)
}));

const ComposerWrapper = styled('div', { name: 'ComposerWrapper' })(
  ({ theme }) => ({
    display: 'flex',
    width: '100%',
    [theme.breakpoints.down('sm')]: {
      display: 'block',
      width: '100%'
    }
  })
);

const ComposerInput = styled('div', { name: 'ComposerInput' })(({ theme }) => ({
  flex: 1,
  backgroundColor: theme.palette.action.hover,
  height: theme.spacing(6),
  borderRadius: 24,
  padding: theme.spacing(0, 3),
  cursor: 'pointer',
  color: theme.palette.text.secondary,
  fontSize: theme.mixins.pxToRem(15),
  fontWeight: theme.typography.fontWeightRegular,
  letterSpacing: 0,
  WebkitBoxOrient: 'vertical',
  WebkitLineClamp: '1',
  display: '-webkit-box',
  overflow: 'hidden',
  textOverflow: 'ellipsis',
  lineHeight: theme.mixins.pxToRem(48),
  [theme.breakpoints.down('sm')]: {
    height: theme.spacing(4),
    lineHeight: theme.mixins.pxToRem(32),
    padding: theme.spacing(0, 2)
  }
}));

const ComposerToolbar = styled('div', { name: 'ComposerToolbar' })(
  ({ theme }) => ({
    display: 'flex',
    marginTop: theme.spacing(1),
    marginLeft: theme.spacing(1.5),
    [theme.breakpoints.down('sm')]: {
      marginLeft: theme.spacing(0)
    }
  })
);

const ComposerToolbarExpand = styled('div', { name: 'ComposerToolbarExpand' })(
  ({ theme }) => ({
    display: 'flex',
    borderTop: 'solid 1px',
    borderTopColor: theme.palette.border?.secondary,
    marginTop: theme.spacing(2),
    marginLeft: theme.spacing(8),
    paddingTop: theme.spacing(1)
  })
);

export default function StatusComposer({
  item,
  title,
  variant,
  blockProps,
  showWhen
}: Props) {
  const [composerState, , composerRef] = useStatusComposer();
  const {
    i18n,
    useSession,
    dispatch,
    jsxBackend,
    usePageParams,
    getAcl,
    getSetting,
    useIsMobile
  } = useGlobal();
  const acl = getAcl();
  const canCreate = getAcl('activity.feed.create');
  const setting = getSetting();
  const { user: authUser, loggedIn } = useSession();
  const pageParams = usePageParams();
  const placeholder = i18n.formatMessage(
    { id: 'what_s_your_mind' },
    { user: authUser?.first_name }
  );
  const composerStatusValue =
    useGetItem('formValues.dialogStatusComposer') || placeholder;

  const { identity: parentIdentity, item_type: parentType } = pageParams;
  const parentId = parentIdentity ? parentIdentity.split('.')[3] : '';
  const isUserProfileOther =
    parentType === 'user' && parentId && authUser?.id !== parseInt(parentId);

  React.useEffect(() => {
    // skip when user does not logged in
    if (!loggedIn) return;

    dispatch({
      type: 'setting/sharingItemPrivacy/FETCH',
      payload: { id: authUser.id }
    });
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [loggedIn]);

  composerRef.current.requestComposerUpdate = React.useCallback(() => {
    setImmediate(() => {
      const { attachmentType, attachments } = composerRef.current.state;

      dispatch({
        type: 'statusComposer/onPress/status',
        payload: {
          data: {
            attachmentType,
            attachments: {
              [attachmentType]: attachments[attachmentType]
            }
          },
          parentIdentity,
          parentType
        }
      });
    });
  }, [composerRef, dispatch, parentIdentity, parentType]);

  const viewTypePrivacy = shouldShowTypePrivacy(parentIdentity, parentType);
  let dataStatusComposer = undefined;

  if (viewTypePrivacy && item?.privacy_detail) {
    dataStatusComposer = { privacy_detail: item.privacy_detail };
  }

  const handleClick = () => {
    dispatch({
      type: 'statusComposer/onPress/status',
      payload: {
        parentIdentity,
        parentType,
        data: dataStatusComposer
      }
    });
  };

  const handleResetRef = () => {
    composerRef.current.removeAttachments();
  };

  const condition = React.useMemo(
    () => ({ strategy, acl, setting, isUserProfileOther, item, parentType }),
    [acl, setting, isUserProfileOther, item, parentType]
  );

  const attachers = filterShowWhen(composerConfig.attachers, condition);
  const isMobile = useIsMobile();

  if (
    isEmpty(authUser) ||
    !canCreate ||
    item?.profile_settings?.profile_view_profile === false ||
    item?.profile_settings?.feed_share_on_wall === false
  )
    return null;

  const show = !!filterShowWhen([{ showWhen }], { item }).length;

  if (!show) return null;

  if (variant === 'expanded')
    return (
      <Block testid="blockStatusComposer">
        <BlockHeader title={title} />
        <BlockContent>
          <Box display="flex" flexDirection="row">
            <AvatarWrapper>
              <UserAvatar
                user={authUser}
                size={isMobile ? 32 : 48}
                data-testid="userAvatar"
              />
            </AvatarWrapper>
            <ComposerInput
              data-testid="whatsHappening"
              color="info"
              onClick={handleClick}
            >
              {composerStatusValue}
            </ComposerInput>
          </Box>
          <ComposerToolbarExpand onClick={handleResetRef}>
            {attachers.map(attacher =>
              jsxBackend.render({
                component: attacher.as,
                props: {
                  key: attacher.as,
                  strategy,
                  composerRef,
                  composerState,
                  control: Control,
                  subject: item
                }
              })
            )}
          </ComposerToolbarExpand>
        </BlockContent>
      </Block>
    );

  return (
    <Block testid="blockStatusComposer">
      <BlockHeader title={title} />
      <BlockContent>
        <Box display="flex" flexDirection="row">
          <AvatarWrapper>
            <UserAvatar
              user={authUser}
              size={isMobile ? 32 : 48}
              data-testid="userAvatar"
            />
          </AvatarWrapper>
          <ComposerWrapper>
            <ComposerInput data-testid="whatsHappening" onClick={handleClick}>
              {composerStatusValue}
            </ComposerInput>
            <ComposerToolbar onClick={handleResetRef}>
              {attachers.map(attacher =>
                jsxBackend.render({
                  component: attacher.as,
                  props: {
                    key: attacher.as,
                    strategy,
                    composerRef,
                    composerState,
                    control: Control,
                    subject: item
                  }
                })
              )}
            </ComposerToolbar>
          </ComposerWrapper>
        </Box>
      </BlockContent>
    </Block>
  );
}
