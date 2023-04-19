import {
  BlockViewProps,
  Link,
  MenuShape,
  useGlobal,
  useSession
} from '@metafox/framework';
import {
  MEMBERSHIP,
  MEMBERSHIP_CONFIRM_AWAIT,
  NOT_MEMBERSHIP,
  REG_METHOD_SECRET
} from '@metafox/group';
import { Block, BlockContent } from '@metafox/layout';
import {
  Container,
  LineIcon,
  ProfileMenu,
  SponsorFlag,
  StickyBar
} from '@metafox/ui';
import { UserItemShape } from '@metafox/user';
import { filterShowWhen, getImageSrc } from '@metafox/utils';
import { Avatar, Box, Button, styled, Typography } from '@mui/material';
import { isEmpty, nth } from 'lodash';
import React from 'react';
import { GroupItemShape } from '../../types';
import InviteCard from './InviteCard';
import { LoadingSkeleton } from './LoadingSkeleton';
import MutedCard from './MutedCard';
import useStyles from './styles';

export interface Props extends BlockViewProps {
  item: GroupItemShape;
  user: UserItemShape;
  identity: string;
  profileMenu: MenuShape;
  profileActionMenu: MenuShape;
  handleAction: any;
  actions: any;
}

const StyledReactButtonWrapper = styled(Box)(({ theme }) => ({
  display: 'flex',
  justifyContent: 'end',
  alignItems: 'center',
  marginLeft: theme.spacing(4),
  '& button': {
    fontWeight: 'bold',
    boxShadow: 'none',
    width: 'max-content',
    marginLeft: theme.spacing(1),
    '& .ico': {
      fontSize: theme.mixins.pxToRem(15)
    }
  },
  [theme.breakpoints.down('sm')]: {
    justifyContent: 'center',
    flexFlow: 'row wrap',
    minHeight: 40,
    padding: '4px 0',
    marginLeft: '0',
    '& button': {
      minWidth: '120px'
    }
  }
}));

const StyledSummary = styled(Typography)(({ theme }) => ({
  fontWeight: 'normal',
  paddingTop: theme.spacing(1)
}));

const StyledGroupInfoWrapper = styled(Box)(({ theme }) => ({
  backgroundColor: theme.mixins.backgroundColor('paper'),
  padding: theme.spacing(2, 2, 2)
}));

const StyledGroupInfo = styled(Box)(({ theme }) => ({
  paddingTop: theme.spacing(0.5),
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'flex-start',
  [theme.breakpoints.down('sm')]: {
    flexFlow: 'column',
    width: '100%',
    alignItems: 'center'
  }
}));

const StyledUserInfo = styled(Box)(({ theme }) => ({
  display: 'flex',
  justifyContent: 'space-between',
  alignItems: 'flex-start',
  [theme.breakpoints.down('sm')]: {
    flexFlow: 'column',
    width: '100%',
    alignItems: 'flex-start',
    marginBottom: theme.spacing(2)
  }
}));

const StyledNotice = styled(Box)(({ theme }) => ({
  marginTop: theme.spacing(2),
  '> div + div': {
    marginTop: theme.spacing(2)
  }
}));

const FeaturedIcon = styled(LineIcon, { name: 'FeaturedIcon' })(
  ({ theme }) => ({
    color: theme.palette.primary.main,
    marginLeft: theme.spacing(2),
    fontSize: 24
  })
);

export default function GroupProfileHeaderView({
  item,
  user,
  identity,
  profileMenu,
  profileActionMenu,
  handleAction,
  actions,
  state
}: Props) {
  const {
    i18n,
    usePageParams,
    ProfileHeaderCover,
    ItemActionMenu,
    dispatch,
    jsxBackend,
    getAcl,
    getSetting,
    assetUrl
  } = useGlobal();
  const acl = getAcl();
  const setting = getSetting();
  const { tab = 'home' } = usePageParams();
  const classes = useStyles();
  const { user: authorUser } = useSession();
  const PendingPreview = jsxBackend.get('group.itemView.pendingReviewCard');
  const PendingGroupPreview = jsxBackend.get(
    'group.itemView.pendingReviewGroupCard'
  );

  if (!item) {
    return <LoadingSkeleton />;
  }

  const {
    cover_photo_id,
    extra,
    title,
    id,
    statistic,
    cover_photo_position,
    membership,
    reg_method
  } = item;
  const avatar =
    getImageSrc(item.cover, '240') || getImageSrc(item.image, '240');

  const condition = { item, acl, setting };

  const profileMenuItems = filterShowWhen(profileMenu.items, condition);
  const actionMenuItems = filterShowWhen(profileActionMenu.items, {
    item: { ...item, is_admin: user?.id === authorUser?.id },
    acl,
    setting
  });

  const itemButtonMembership =
    nth(actionMenuItems, 0)?.name !== 'manage'
      ? nth(actionMenuItems, 0)
      : undefined;

  const membershipStatus = [
    MEMBERSHIP,
    MEMBERSHIP_CONFIRM_AWAIT,
    NOT_MEMBERSHIP
  ].includes(membership)
    ? itemButtonMembership
    : undefined;

  const actionMoreItems = membershipStatus
    ? actionMenuItems.slice(1)
    : actionMenuItems;

  const cover = getImageSrc(
    item.cover,
    '1024',
    assetUrl('group.cover_no_image')
  );

  const handleSearch = () => {
    dispatch({ type: 'group/search', payload: { identity } });
  };

  const handleShowLoginDialog = () => {
    dispatch({ type: 'user/showDialogLogin' });
  };

  const inviteFriends = () => {
    dispatch({ type: 'group/inviteFriends', payload: { identity } });
  };

  return (
    <Block>
      <BlockContent>
        <Box>
          <ProfileHeaderCover
            identity={identity}
            image={cover}
            imageId={cover_photo_id}
            alt={title}
            left={0}
            top={+cover_photo_position || 0}
          />
          <Box>
            <Container maxWidth="md" disableGutters sx={{ padding: 0 }}>
              <StyledGroupInfoWrapper>
                <SponsorFlag variant="itemView" value={item.is_sponsor} />
                <StyledGroupInfo>
                  <StyledUserInfo>
                    <Box>
                      <Typography variant="h2">
                        {item.title}
                        {item.is_featured ? (
                          <FeaturedIcon icon="ico-check-circle" />
                        ) : null}
                      </Typography>
                      <StyledSummary variant="subtitle1" color="text.secondary">
                        {item.reg_name}
                        {statistic?.total_member && extra?.can_view_members ? (
                          <>
                            {' '}
                            &middot;{' '}
                            <Link
                              to={`/group/${item.id}/member?stab=all_members`}
                            >
                              {i18n.formatMessage(
                                { id: 'total_member' },
                                { value: statistic.total_member }
                              )}
                            </Link>
                          </>
                        ) : null}
                      </StyledSummary>
                    </Box>
                  </StyledUserInfo>
                  <StyledReactButtonWrapper>
                    {membershipStatus && (
                      <Button
                        size="small"
                        variant={membershipStatus?.variant}
                        disabled={membershipStatus?.disabled}
                        startIcon={<LineIcon icon={membershipStatus?.icon} />}
                        color="primary"
                        onClick={
                          isEmpty(authorUser)
                            ? handleShowLoginDialog
                            : () => handleAction(membershipStatus?.value)
                        }
                        className={classes.buttonJoin}
                      >
                        {membershipStatus?.label}
                      </Button>
                    )}
                    {reg_method !== REG_METHOD_SECRET ? (
                      <Button
                        onClick={inviteFriends}
                        size="small"
                        variant="outlined"
                        startIcon={
                          <LineIcon
                            sx={{ marginLeft: '0 !important' }}
                            icon={'ico-envelope'}
                          />
                        }
                      >
                        <span>
                          {i18n.formatMessage({ id: 'invite_friends' })}
                        </span>
                      </Button>
                    ) : (
                      <ItemActionMenu
                        menuName="itemActionInviteMenu"
                        state={state}
                        handleAction={handleAction}
                        control={
                          <Button
                            size="small"
                            variant="outlined"
                            startIcon={
                              <LineIcon
                                sx={{ marginLeft: '0 !important' }}
                                icon={'ico-envelope'}
                              />
                            }
                          >
                            <span>{i18n.formatMessage({ id: 'invite' })}</span>
                          </Button>
                        }
                      />
                    )}
                  </StyledReactButtonWrapper>
                </StyledGroupInfo>
                <StyledNotice>
                  <InviteCard item={item} actions={actions} />
                  <PendingPreview item={item} actions={actions} />
                  <PendingGroupPreview item={item} actions={actions} />
                  <MutedCard item={item} />
                </StyledNotice>
              </StyledGroupInfoWrapper>
            </Container>
            <StickyBar>
              {({ sticky }) => (
                <Container
                  maxWidth="md"
                  disableGutters
                  className={classes.profileHeaderContainer}
                >
                  {sticky ? (
                    <div className={classes.profileHeaderBottom}>
                      <div className={classes.wrapperMenu}>
                        <div className={classes.userStickyWrapper}>
                          <Avatar
                            className={classes.userAvatarSticky}
                            src={avatar}
                          />
                          <div className={classes.userNameSticky}>
                            {item.title}
                          </div>
                        </div>
                      </div>
                      <div className={classes.actionButtons}>
                        {item.reg_method === 0 ||
                        membershipStatus?.name === 'joined' ? (
                          <Button
                            variant={'outlined'}
                            color="primary"
                            onClick={handleSearch}
                            sx={{ display: 'none' }}
                          >
                            <LineIcon icon={'ico-search-o'} />
                          </Button>
                        ) : null}
                        <ItemActionMenu
                          id="actionMenu"
                          label="ActionMenu"
                          handleAction={handleAction}
                          items={actionMoreItems}
                          control={
                            <Button
                              variant="outlined"
                              color="primary"
                              size="large"
                            >
                              <LineIcon icon={'ico-dottedmore-o'} />
                            </Button>
                          }
                        />
                      </div>
                    </div>
                  ) : (
                    <div className={classes.profileHeaderBottom}>
                      <div className={classes.wrapperMenu}>
                        <div className={classes.profileMenu}>
                          <ProfileMenu
                            items={profileMenuItems}
                            activeTab={tab}
                            prefix={`/group/${id}`}
                            maxDisplayTab={5}
                          />
                        </div>
                      </div>
                      <div className={classes.actionButtons}>
                        {item.reg_method === 0 ||
                        membershipStatus?.name === 'joined' ? (
                          <Button
                            variant={'outlined'}
                            color="primary"
                            onClick={handleSearch}
                          >
                            <LineIcon icon={'ico-search-o'} />
                          </Button>
                        ) : null}
                        <ItemActionMenu
                          id="actionMenu"
                          label="ActionMenu"
                          handleAction={handleAction}
                          items={actionMoreItems}
                          control={
                            <Button
                              variant="outlined"
                              color="primary"
                              size="large"
                            >
                              <LineIcon icon={'ico-dottedmore-o'} />
                            </Button>
                          }
                        />
                      </div>
                    </div>
                  )}
                </Container>
              )}
            </StickyBar>
          </Box>
        </Box>
      </BlockContent>
    </Block>
  );
}

GroupProfileHeaderView.LoadingSkeleton = LoadingSkeleton;
