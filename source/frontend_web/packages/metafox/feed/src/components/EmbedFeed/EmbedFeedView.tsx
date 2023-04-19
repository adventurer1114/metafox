import { GlobalState, Link, useGlobal } from '@metafox/framework';
import { FromNow, LineIcon, PrivacyIcon, UserAvatar } from '@metafox/ui';
import { styled } from '@mui/material';
import clsx from 'clsx';
import { get, isString } from 'lodash';
import * as React from 'react';
import { useSelector } from 'react-redux';
import { FeedItemViewProps } from '../../types';
import FeedEmbedObjectView from '../EmbedObject';
import TaggedFriends from '../FeedItemView/FeedItemTaggedFriends';
import TaggedPlace from '../FeedItemView/FeedItemTaggedPlace';
import ProfileLink from '../FeedItemView/ProfileLink';
import FeedStatusView from '../FeedStatus';
import HeadlineInfo from '../HeadlineInfo';
import useStyles from './styles';

const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(({ theme }) => ({
  marginRight: theme.spacing(1.5)
}));

export default function EmbedFeedInFeed({
  item,
  user,
  parent_user
}: FeedItemViewProps) {
  const { i18n } = useGlobal();
  const classes = useStyles();

  const tagged_friends_list = useSelector((state: GlobalState) => {
    if (!item) return null;

    return item.tagged_friends?.map(item => get(state, item));
  });

  // ensure item exists.
  if (!item) return null;

  const { info, embed_object, status, status_background, location } = item;

  // do not embed feed in feed.
  if (isString(embed_object) && embed_object.startsWith('feed')) return null;

  if (!item || !user) return null;

  let noMarginBottom = false;
  let paddingTopMedia = false;

  const media =
    isString(embed_object) &&
    ['photo', 'video'].includes(embed_object?.split('.')[0]);

  if (media) {
    noMarginBottom = true;
    paddingTopMedia = true;
  }

  return (
    <div
      className={clsx(classes.root, noMarginBottom && classes.noMarginBottom)}
    >
      {media ? <FeedEmbedObjectView embed={embed_object} feed={item} /> : null}
      <div
        className={clsx(
          classes.header,
          paddingTopMedia && classes.paddingTopMedia
        )}
      >
        <AvatarWrapper>
          <UserAvatar user={user} size={48} />
        </AvatarWrapper>
        <div className={classes.headerInfo}>
          <div className={classes.headerHeadline}>
            <span className={classes.headlineSpan}>
              <Link
                to={`/${user.user_name}`}
                children={user.full_name}
                hoverCard={`/user/${user.id}`}
                className={classes.profileLink}
              />
            </span>
            <HeadlineInfo
              info={info}
              embed_object={embed_object}
              classes={classes}
              item={item}
            />
            {parent_user ? (
              <span className={classes.headlineSpan}>
                {i18n.formatMessage(
                  {
                    id: 'to_parent_user'
                  },
                  {
                    icon: () => (
                      <LineIcon
                        icon="ico-caret-right"
                        className={classes.caretIcon}
                      />
                    ),
                    parent_user: () => (
                      <ProfileLink
                        user={parent_user}
                        className={classes.profileLink}
                      />
                    )
                  }
                )}
              </span>
            ) : null}
            {tagged_friends_list?.length ? (
              <span className={classes.headlineSpan}>
                <TaggedFriends
                  item_type={item.item_type}
                  item_id={item.item_id}
                  total={item.total_friends_tagged}
                  users={tagged_friends_list}
                  className={classes.profileLink}
                />
              </span>
            ) : null}
            {location ? (
              <span className={classes.headlineSpan}>
                {i18n.formatMessage(
                  {
                    id: 'at_tagged_place'
                  },
                  {
                    name: location.address,
                    bold: () => (
                      <TaggedPlace
                        place={location}
                        className={classes.profileLink}
                      />
                    )
                  }
                )}
              </span>
            ) : null}
          </div>
          <div className={classes.privacyBlock}>
            <span className={classes.separateSpans}>
              <PrivacyIcon value={item.privacy} item={item.privacy_detail} />
              <FromNow value={item.creation_date} />
            </span>
          </div>
        </div>
      </div>
      <FeedStatusView
        status={status}
        backgroundImage={status_background}
        classes={classes}
      />
      {!media ? <FeedEmbedObjectView embed={embed_object} feed={item} /> : null}
    </div>
  );
}
