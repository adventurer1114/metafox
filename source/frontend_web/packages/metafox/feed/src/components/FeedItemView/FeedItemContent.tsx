/**
 * @type: itemView
 * name: feed.itemView.content
 */
import PrivacyControl from '@metafox/feed/dialogs/StatusComposer/PrivacyControl';
import { getTaggedFriendsSelector } from '@metafox/feed/selectors/feed';
import {
  getItemSelector,
  GlobalState,
  Link,
  useGlobal
} from '@metafox/framework';
import { FromNow, ItemUserShape, PrivacyIcon, UserAvatar } from '@metafox/ui';
import { Divider, styled } from '@mui/material';
import * as React from 'react';
import { useIntl } from 'react-intl';
import { useSelector } from 'react-redux';
import { FeedItemShape, FeedItemViewState } from '../../types';
import FeedEmbedObjectView from '../EmbedObject';
import FeedStatus from '../FeedStatus';
import HeadlineInfo from '../HeadlineInfo';
import HeadlineSpan from '../HeadlineSpan';
import TaggedFriends from './FeedItemTaggedFriends';
import TaggedPlace from './FeedItemTaggedPlace';
import { LoadingSkeleton } from './LoadingSkeleton';
import ProfileLink from './ProfileLink';
import useStyles from './styles';

export const AvatarWrapper = styled('div', { name: 'AvatarWrapper' })(
  ({ theme }) => ({
    marginRight: theme.spacing(1.5)
  })
);

type Props = {
  handleAction: any;
  state: FeedItemViewState;
  menuName?: string;
  identity: string;
  setVisible: (value: boolean) => void;
  isItemAction?: boolean;
};

const FeedItemContent = ({
  identity,
  handleAction,
  state,
  menuName = 'itemActionMenu',
  setVisible,
  isItemAction = true
}: Props) => {
  const classes = useStyles();
  const { ItemActionMenu, dispatch } = useGlobal();
  const i18n = useIntl();

  const item: FeedItemShape = useSelector((state: GlobalState) =>
    getItemSelector(state, identity)
  );
  const user: ItemUserShape = useSelector((state: GlobalState) =>
    getItemSelector(state, item?.user)
  );
  const embed_object = useSelector((state: GlobalState) =>
    getItemSelector(state, item?.embed_object)
  );
  const tagged_friends = useSelector((state: GlobalState) =>
    getTaggedFriendsSelector(state, item)
  );

  if (!item || !user) return null;

  const {
    info,
    item_id,
    status,
    status_background,
    location,
    item_type,
    total_friends_tagged,
    extra,
    link
  } = item;

  return (
    <>
      <div className={classes.header}>
        <AvatarWrapper>
          <UserAvatar user={user as any} size={48} />
        </AvatarWrapper>
        <div className={classes.headerInfo}>
          <div className={classes.headerHeadline}>
            <HeadlineSpan>
              <ProfileLink user={user} className={classes.profileLink} />
            </HeadlineSpan>
            <HeadlineInfo
              info={info}
              embed_object={embed_object}
              item={item}
              classes={classes}
            />
            {tagged_friends?.length ? (
              <HeadlineSpan>
                <TaggedFriends
                  item_type={item_type}
                  item_id={item_id}
                  total={total_friends_tagged}
                  users={tagged_friends}
                  className={classes.profileLink}
                />
              </HeadlineSpan>
            ) : null}
            {location && item_type !== 'event' ? (
              <HeadlineSpan>
                {i18n.formatMessage(
                  {
                    id: 'at_tagged_place'
                  },
                  {
                    name: location.address,
                    bold: () => (
                      <TaggedPlace
                        place={location}
                        className={classes.locationLink}
                      />
                    )
                  }
                )}
              </HeadlineSpan>
            ) : null}
          </div>
          <div className={classes.privacyBlock}>
            <span className={classes.separateSpans}>
              <span>
                <Link color="inherit" to={link}>
                  <FromNow value={item.creation_date} />
                </Link>
              </span>
              {extra?.can_change_privacy_from_feed ? (
                <span>
                  <PrivacyControl
                    showDropdown={false}
                    setValue={value => {
                      dispatch({
                        type: 'updatePrivacyFeedItem',
                        payload: { identity, privacy: value }
                      });
                    }}
                    value={item.privacy}
                    item={item.privacy_detail}
                    showLabel={false}
                    feed={item}
                  />
                </span>
              ) : (
                <PrivacyIcon item={item.privacy_detail} />
              )}
            </span>
          </div>
        </div>
        {isItemAction && (
          <ItemActionMenu
            identity={identity}
            state={state}
            handleAction={handleAction}
            className={classes.headerActionMenu}
            menuName={menuName}
            zIndex={999}
          />
        )}
      </div>
      <FeedStatus
        status={status}
        backgroundImage={status_background}
        classes={classes}
      />
      {!embed_object && item.embed_object?.length !== 0 ? <Divider /> : null}
      <FeedEmbedObjectView
        embed={item.embed_object}
        feed={item}
        setVisible={setVisible}
      />
    </>
  );
};

FeedItemContent.LoadingSkeleton = LoadingSkeleton;

export default FeedItemContent;
