import { BlockViewProps, Link, MenuShape, useGlobal } from '@metafox/framework';
import { Block, BlockContent } from '@metafox/layout';
import { Container, LineIcon, ProfileMenu, StickyBar } from '@metafox/ui';
import {
  colorHash,
  filterShowWhen,
  getImageSrc,
  shortenFullName
} from '@metafox/utils';
import { Avatar, Box, Button, styled, Typography } from '@mui/material';
import { nth, isEmpty } from 'lodash';
import React from 'react';
import { PagesItemShape } from '../../types';
import { LoadingSkeleton } from './LoadingSkeleton';
import useStyles from './styles';
import PendingCard from './PendingCard';
import InviteCard from './InviteCard';
import { INVITE, LIKE, LIKED, MANAGE } from '@metafox/pages/constant';

export interface Props extends BlockViewProps {
  item: PagesItemShape;
  identity: string;
  profileMenu: MenuShape;
  profileActionMenu: MenuShape;
  handleAction: any;
  state: any;
}

const FeaturedIcon = styled(LineIcon, { name: 'FeaturedIcon' })(
  ({ theme }) => ({
    color: theme.palette.primary.main,
    marginLeft: theme.spacing(2),
    fontSize: 24
  })
);

export default function PageProfileHeaderView({
  item,
  identity,
  profileMenu,
  profileActionMenu,
  blockProps,
  handleAction,
  actions
}: Props) {
  const {
    i18n,
    usePageParams,
    ProfileHeaderCover,
    ProfileHeaderAvatar,
    ItemActionMenu,
    dispatch,
    getAcl,
    getSetting,
    useSession,
    assetUrl
  } = useGlobal();
  const session = useSession();
  const acl = getAcl();
  const setting = getSetting();
  const { tab = 'home' } = usePageParams();
  const classes = useStyles();

  if (!item?.statistic) {
    return <LoadingSkeleton />;
  }

  const {
    cover_photo_id,
    extra,
    full_name,
    id,
    statistic,
    cover_photo_position,
    external_link
  } = item;

  const bgColor = colorHash.hex(shortenFullName(full_name) || '');
  const condition = { item, acl, setting, session };

  const listNoMore = [LIKE, LIKED, INVITE];

  const profileMenuItems = filterShowWhen(profileMenu.items, condition);

  const actionMenuItems = filterShowWhen(profileActionMenu.items, condition);

  const moreItemsAction = actionMenuItems.filter(
    item => !listNoMore.includes(item.name)
  );

  const actionButtons = actionMenuItems?.filter(item => item.name === INVITE);

  const likeButton = nth(actionMenuItems, 0);

  const avatar = getImageSrc(item.image, '200x200', assetUrl('page.no_image'));
  const cover = getImageSrc(
    item?.cover,
    '1024',
    assetUrl('page.cover_no_image')
  );

  const handleSearch = () => {
    dispatch({ type: 'page/search', payload: { identity } });
  };

  const onEditAvatar = () => {
    dispatch({ type: 'editProfileHeaderAvatar', payload: { identity } });
  };

  const handleShowLoginDialog = () => {
    dispatch({ type: 'user/showDialogLogin' });
  };

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
                  className={classes.profileHeaderContainer}
                  disableGutters
                >
                  <Box className={classes.userContainer}>
                    <div className={classes.userInfoContainer}>
                      <Box
                        display="flex"
                        justifyContent="space-between"
                        alignItems="flex-start"
                        className={classes.userInfo}
                      >
                        <ProfileHeaderAvatar
                          alt={shortenFullName(item.title)}
                          canEdit={extra?.can_edit}
                          onEdit={onEditAvatar}
                          avatar={avatar}
                          avatarId={item.image_id}
                        />
                        <div>
                          <Typography variant="h2">
                            {item.title}
                            {item.is_featured ? (
                              <FeaturedIcon icon="ico-check-circle" />
                            ) : null}
                          </Typography>
                          <div className={classes.summary}>
                            {item.summary}
                            {statistic.total_like ? (
                              <>
                                {' '}
                                &middot;{' '}
                                <Link
                                  to={`/page/${item.id}/member?stab=all_members`}
                                >
                                  {i18n.formatMessage(
                                    { id: 'total_like' },
                                    { value: statistic.total_like }
                                  )}
                                </Link>
                              </>
                            ) : null}
                          </div>
                        </div>
                      </Box>
                      <div className={classes.wrapperButtonInline}>
                        {likeButton.name !== MANAGE && (
                          <Button
                            disabled={
                              likeButton.name === LIKED && item?.is_owner
                            }
                            disableElevation
                            variant="contained"
                            size="medium"
                            startIcon={<LineIcon icon={likeButton.icon} />}
                            color={likeButton.color as any}
                            onClick={
                              isEmpty(session?.user)
                                ? handleShowLoginDialog
                                : () => handleAction(likeButton.value)
                            }
                          >
                            {likeButton.label}
                          </Button>
                        )}
                        {external_link ? (
                          <Link
                            href={external_link}
                            color={'text.hint'}
                            variant="body1"
                            rel="noopener noreferrer"
                            target="_blank"
                            underline="none"
                            sx={{
                              overflow: 'hidden',
                              textOverflow: 'ellipsis',
                              display: '-webkit-box',
                              wordBreak: 'break-all',
                              '-webkit-box-orient': 'vertical',
                              '-webkit-line-clamp': '1',
                              mt: 2
                            }}
                          >
                            {external_link}
                          </Link>
                        ) : null}
                      </div>
                    </div>
                    {item?.is_pending || item?.membership ? (
                      <Box sx={{ mt: 2.5 }}>
                        <InviteCard item={item} actions={actions} />
                        <PendingCard item={item} actions={actions} />
                      </Box>
                    ) : null}
                  </Box>
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
                                children={shortenFullName(item.title)}
                                style={{ backgroundColor: bgColor }}
                              />
                              <div className={classes.userNameSticky}>
                                {item.title}
                              </div>
                            </div>
                          </div>
                          <div className={classes.actionButtons}>
                            {actionButtons.map((btn, index) => (
                              <Button
                                key={btn.label}
                                variant={'outlined'}
                                startIcon={<LineIcon icon={btn.icon} />}
                                color="primary"
                                sx={{ width: '100%' }}
                                onClick={() =>
                                  dispatch({
                                    type: btn.value,
                                    payload: {
                                      identity
                                    }
                                  })
                                }
                              >
                                {btn.label}
                              </Button>
                            ))}
                            <Button variant={'outlined'} color="primary">
                              <LineIcon icon={'ico-search-o'} />
                            </Button>
                            <ItemActionMenu
                              id="actionMenu"
                              label="ActionMenu"
                              handleAction={handleAction}
                              items={moreItemsAction}
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
                                prefix={`/page/${id}`}
                                maxDisplayTab={4}
                              />
                            </div>
                          </div>
                          <div className={classes.actionButtons}>
                            {actionButtons.map((btn, index) => (
                              <Button
                                key={btn.label}
                                variant={'outlined'}
                                startIcon={<LineIcon icon={btn.icon} />}
                                color="primary"
                                sx={{ width: '100%' }}
                                onClick={() =>
                                  dispatch({
                                    type: btn.value,
                                    payload: {
                                      identity
                                    }
                                  })
                                }
                              >
                                {btn.label}
                              </Button>
                            ))}
                            <Button
                              variant={'outlined'}
                              color="primary"
                              onClick={handleSearch}
                            >
                              <LineIcon icon={'ico-search-o'} />
                            </Button>
                            <ItemActionMenu
                              id="actionMenu"
                              label="ActionMenu"
                              handleAction={handleAction}
                              items={moreItemsAction}
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
              </div>
            </Box>
          </div>
        </div>
      </BlockContent>
    </Block>
  );
}

PageProfileHeaderView.LoadingSkeleton = LoadingSkeleton;
