/* eslint-disable @typescript-eslint/no-unused-vars */
import { BlockViewProps, MenuShape, useGlobal } from '@metafox/framework';
import HtmlViewer from '@metafox/html-viewer';
import { Block, BlockContent } from '@metafox/layout';
import {
  Container,
  LineIcon,
  ProfileMenu,
  StickyBar,
  UserAvatar
} from '@metafox/ui';
import { filterShowWhen, getImageSrc, shortenFullName } from '@metafox/utils';
import { Box, Button, styled } from '@mui/material';
import React from 'react';
import { UserItemActions, UserItemShape } from '../../types';
import ActivityPointSummary from './ActivityPointSummary';
import { LoadingSkeleton } from './LoadingSkeleton';
import useStyles from './styles';

const ItemSummary = styled('div', { name: 'ItemSummary' })(({ theme }) => ({
  color: theme.palette.text.secondary,
  fontSize: theme.mixins.pxToRem(15),
  paddingTop: theme.spacing(1),
  paddingRight: theme.spacing(2),
  [theme.breakpoints.down('sm')]: {
    paddingRight: 0
  }
}));

const FeaturedIcon = styled(LineIcon, { name: 'FeaturedIcon' })(
  ({ theme }) => ({
    color: theme.palette.primary.main,
    marginLeft: theme.spacing(2)
  })
);
const UserStickyWrapper = styled('div', { name: 'UserStickyWrapper' })(
  ({ theme }) => ({
    position: 'relative',
    left: 16,
    transition: 'all .2s',
    '& a': {
      borderWidth: 'thin',
      borderStyle: 'solid',
      borderColor: theme.palette.border.secondary
    }
  })
);

const ProfileMenuStyled = styled('div', {
  name: 'ProfileMenuStyled',
  shouldForwardProp: prop => prop !== 'sticky'
})<{ sticky?: any }>(({ theme, sticky }) => ({
  flex: 1,
  minWidth: 0,
  transition: 'all .2s',
  ...(sticky && {
    marginLeft: theme.spacing(2.5),
    transition: 'all .2s'
  })
}));

export interface Props extends BlockViewProps {
  item: UserItemShape;
  identity: string;
  profileMenu: MenuShape;
  profileActionMenu: MenuShape;
  handleAction: any;
  state: any;
  actions: UserItemActions;
}

const UserProfileHeaderView = ({
  item,
  identity,
  profileMenu,
  profileActionMenu,
  blockProps,
  handleAction,
  state,
  actions
}: Props) => {
  const {
    ItemActionMenu,
    usePageParams,
    ProfileHeaderCover,
    ProfileHeaderAvatar,
    useSession,
    getAcl,
    getSetting,
    assetUrl
  } = useGlobal();
  const { id: user_id, tab = 'home' } = usePageParams();
  const classes = useStyles();
  const { user: userAuth } = useSession();
  const acl = getAcl();
  const setting = getSetting();

  if (!item?.profile_menu_settings || !item?.profile_settings) {
    return <LoadingSkeleton />;
  }

  const { cover_photo_id, extra, cover_photo_position, profile_settings } =
    item;

  const avatar = getImageSrc(item.avatar, '200', assetUrl('user.no_image'));
  const cover = getImageSrc(
    item?.cover,
    '1024',
    assetUrl('user.cover_no_image')
  );
  const condition = { item, acl, setting };
  const profileMenuItems = filterShowWhen(profileMenu.items, condition);
  const actionMenuItemsFull = filterShowWhen(
    profileActionMenu.items,
    condition
  );
  const actionButtons = actionMenuItemsFull.slice(0, 2);
  const actionMenuItems = actionMenuItemsFull.slice(2);

  return (
    <Block>
      <BlockContent>
        <div className={classes.root}>
          <div className={classes.wrapper}>
            <Box>
              <ProfileHeaderCover
                identity={identity}
                image={cover}
                imageId={cover_photo_id}
                alt={''}
                left={0}
                top={+cover_photo_position || 0}
              />
              <div className={classes.profileUserWrapper}>
                <Container
                  maxWidth="md"
                  disableGutters
                  className={classes.profileHeaderContainer}
                >
                  <Box
                    display="flex"
                    justifyContent="space-between"
                    alignItems="flex-start"
                    p={2}
                    className={classes.userInfoContainer}
                  >
                    <Box
                      display="flex"
                      justifyContent="space-between"
                      alignItems="flex-start"
                      className={classes.userInfo}
                    >
                      <ProfileHeaderAvatar
                        alt={shortenFullName(item.full_name)}
                        canEdit={extra?.can_edit}
                        onEdit={actions.editProfileHeaderAvatar}
                        avatar={avatar}
                        avatarId={item.avatar_id}
                      />
                      <div>
                        <h1 className={classes.title}>
                          {item.full_name}
                          {item.is_featured ? (
                            <FeaturedIcon icon="ico-check-circle" />
                          ) : null}
                        </h1>
                        {!profile_settings?.profile_view_profile ? null : (
                          <ItemSummary>
                            <HtmlViewer html={item.bio} />
                          </ItemSummary>
                        )}
                      </div>
                    </Box>
                    <Box
                      display="flex"
                      flexDirection="column"
                      alignItems={{ sm: 'flex-end', xs: 'flex-start' }}
                    >
                      <div className={classes.wrapperButtonInline}>
                        {actionButtons.map((btn, index) => (
                          <Button
                            key={btn.label}
                            variant={0 === index ? 'contained' : 'outlined'}
                            startIcon={<LineIcon icon={btn.icon} />}
                            onClick={() => handleAction(btn.value)}
                            color={(btn.color || 'primary') as any}
                            size="small"
                          >
                            {btn.label}
                          </Button>
                        ))}
                        {userAuth?.id !== parseInt(user_id) &&
                        actionMenuItems?.length ? (
                          <ItemActionMenu
                            id="actionMenu"
                            label="ActionMenu"
                            handleAction={handleAction}
                            items={actionMenuItems}
                            control={
                              <Button
                                variant="outlined"
                                color="primary"
                                size="small"
                                className={classes.profileActionMenu}
                              >
                                <LineIcon icon={'ico-dottedmore-o'} />
                              </Button>
                            }
                          />
                        ) : null}
                      </div>
                      {extra?.can_view_profile_activity_point ? (
                        <ActivityPointSummary
                          isOwner={userAuth?.id === parseInt(user_id)}
                        />
                      ) : null}
                    </Box>
                  </Box>
                </Container>
              </div>
              {profile_settings?.profile_view_profile && (
                <StickyBar>
                  {({ sticky }) => (
                    <Container
                      maxWidth="md"
                      disableGutters
                      className={classes.profileHeaderContainer}
                    >
                      <div className={classes.wrapperMenu}>
                        {sticky ? (
                          <UserStickyWrapper>
                            <UserAvatar user={item} size={48} />
                          </UserStickyWrapper>
                        ) : null}

                        <ProfileMenuStyled sticky={sticky}>
                          <ProfileMenu
                            items={profileMenuItems}
                            activeTab={tab}
                            maxDisplayTab={5}
                          />
                        </ProfileMenuStyled>
                      </div>
                    </Container>
                  )}
                </StickyBar>
              )}
            </Box>
          </div>
        </div>
      </BlockContent>
    </Block>
  );
};

UserProfileHeaderView.displayName = 'UserProfileHeaderView';

export default UserProfileHeaderView;
