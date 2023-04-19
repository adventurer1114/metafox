import {
  GlobalState,
  Link,
  useGlobal,
  useResourceAction
} from '@metafox/framework';
import { SavedItemProps as ItemProps } from '@metafox/saved/types';
import {
  ItemAction,
  ItemMedia,
  ItemSubInfo,
  ItemText,
  ItemTitle,
  ItemView,
  LineIcon
} from '@metafox/ui';
import { getImageSrc } from '@metafox/utils';
import { Box, Button, IconButton, styled } from '@mui/material';
import { camelCase } from 'lodash';
import { useSelector } from 'react-redux';
import React from 'react';
import MoreCollection from './MoreCollection';
import { APP_SAVED, RESOURCE_SAVED_LIST } from '@metafox/saved/constant';

const name = 'SavedItemView';
const UnOpenedDot = styled('span', { name, slot: 'UnOpenedDot' })(
  ({ theme }) => ({
    display: 'inline-block',
    width: 6,
    height: 6,
    backgroundColor: theme.palette.primary.main,
    borderRadius: 4,
    marginRight: theme.spacing(0.5)
  })
);
const ItemTitleStyled = styled(Box)(({ theme }) => ({
  display: 'flex',
  alignItems: 'center',
  overflow: 'hidden'
}));

export default function SavedItemView({
  item,
  identity,
  handleAction,
  state,
  itemProps,
  user,
  wrapAs,
  wrapProps
}: ItemProps) {
  const {
    ItemActionMenu,
    i18n,
    ShareActButton,
    useSession,
    assetUrl,
    dispatch,
    useGetItem,
    usePageParams
  } = useGlobal();
  const session = useSession();
  const { collection_id } = usePageParams();
  const shareMenu = useSelector(
    (state: GlobalState) => state.share.shareOptions || []
  );
  const identityFirstCollection = `saved.entities.saved_list.${item?.default_collection_id}`;
  const identityCollectionParam = `saved.entities.saved_list.${collection_id}`;
  const firstCollection = useGetItem(identityFirstCollection);
  const itemCollectionParam = useGetItem(identityCollectionParam);
  const userOwner = useGetItem(item?.user);
  const dataSourceCollections = useResourceAction(
    APP_SAVED,
    RESOURCE_SAVED_LIST,
    'viewItems'
  );

  const isAddToCollection = React.useMemo(() => {
    if (!collection_id) {
      return true;
    }

    if (
      collection_id &&
      (session?.user?.id === userOwner?.id ||
        itemCollectionParam?.extra?.is_owner)
    ) {
      return true;
    }

    return false;
  }, [collection_id, itemCollectionParam, session, userOwner]);

  if (!item || !user) return null;

  const { menuName = 'itemActionMenu' } = itemProps;

  const imgSrc = getImageSrc(item?.image, '240', assetUrl('saved.no_image'));

  const handleMarkOpened = () => {
    dispatch({ type: 'saved/markAsOpened', payload: { identity } });
  };

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={camelCase(`item ${item.resource_name}`)}
    >
      <Box onClick={handleMarkOpened}>
        <ItemMedia
          src={imgSrc}
          link={item.link}
          alt={item.title}
          backgroundImage
        />
      </Box>
      <ItemText>
        <Box sx={{ display: 'flex', justifyContent: 'space-between' }}>
          <ItemTitleStyled>
            <Box>{!item.is_opened ? <UnOpenedDot /> : null}</Box>
            <ItemTitle onClick={handleMarkOpened}>
              <Link to={item.link}>{item.title}</Link>
            </ItemTitle>
          </ItemTitleStyled>
          {itemProps.showActionMenu ? (
            <ItemAction>
              <ItemActionMenu
                menuName={menuName}
                identity={identity}
                icon={'ico-dottedmore-vertical-o'}
                state={state}
                handleAction={handleAction}
                size="smallest"
              />
            </ItemAction>
          ) : null}
        </Box>
        <ItemSubInfo sx={{ color: 'text.secondary', mt: 1 }}>
          <Link
            color="inherit"
            to={user.link}
            children={user.full_name}
            hoverCard={`/user/${user.id}`}
          />
          {item.item_type_name
            ? i18n.formatMessage({ id: item.item_type_name })
            : null}
          {item.belong_to_collection ? (
            <span>
              {i18n.formatMessage({ id: 'added_to' })}{' '}
              <Link
                to={`/saved/list/${item.default_collection_id}`}
                color="text.primary"
                fontWeight="bold"
              >
                {firstCollection?.name || item.default_collection_name}
              </Link>{' '}
              {item.statistic?.total_collection > 1 ? (
                <MoreCollection
                  identity={identity}
                  total={item.statistic?.total_collection}
                  dataSource={dataSourceCollections}
                  excludeCollection={[item.default_collection_id]}
                />
              ) : null}
            </span>
          ) : null}
        </ItemSubInfo>
        <Box>
          {isAddToCollection && (
            <ItemActionMenu
              menuName="addToCollection"
              identity={identity}
              state={state}
              handleAction={handleAction}
              sx={{ fontWeight: 'bold', mr: 1, mt: 2 }}
              control={
                <Button variant="outlined" size="small" color="primary">
                  <LineIcon
                    icon="ico-folder-plus-o"
                    sx={{ mr: 1, fontSize: 15 }}
                  />
                  {i18n.formatMessage({ id: 'add_to_collection' })}
                </Button>
              }
            />
          )}
          {session.loggedIn && item.extra.can_share && ShareActButton ? (
            <ItemActionMenu
              icon="ico-share-o"
              items={shareMenu}
              testid="menuShare"
              identity={identity}
              handleAction={handleAction}
              sx={{ mt: 2 }}
              control={
                <IconButton
                  variant="outlined-square"
                  size="small"
                  color="primary"
                >
                  <LineIcon icon="ico-share-o" sx={{ fontSize: 15 }} />
                </IconButton>
              }
            />
          ) : null}
        </Box>
      </ItemText>
    </ItemView>
  );
}
