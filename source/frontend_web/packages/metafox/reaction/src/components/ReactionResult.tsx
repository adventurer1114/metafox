/**
 * @type: service
 * name: ReactionResult
 */
import { getItemSelector, GlobalState, useGlobal } from '@metafox/framework';
import { styled, Tooltip } from '@mui/material';
import React, { useEffect, useState } from 'react';
import { useSelector } from 'react-redux';
import ReactionIcon from '../containers/ReactionIcon';
import { getReactionItemSelector } from '../selectors/geReactionItem';
import { MostReactionsProps, ReactionItem } from '../types';
import useStyles from './ReactionResult.styles';

export interface StyledWrapperProps {
  size?: 'md' | 'sm';
}

const ItemReaction = styled('span', {
  name: 'ItemReaction'
})(({ theme }) => ({
  display: 'inline-flex',
  boxSizing: 'content-box',
  border: '1px solid',
  borderColor: theme.palette.background.paper,
  borderRadius: '100%',
  lineHeight: 0,
  '& img': {
    width: 'auto',
    height: '100%',
    borderRadius: '100%'
  }
}));

const Wrapper = styled('span', {
  name: 'ReactResultWrapper',
  shouldForwardProp: prop => prop !== 'size'
})<StyledWrapperProps>(({ theme, size }) => ({
  display: 'inline-flex',
  alignItems: 'center',
  ...(size &&
    size === 'md' && {
      '& .itemReaction': {
        width: '24px',
        height: '24px',
        '& + .itemReaction': {
          marginLeft: '-4px'
        }
      },
      '& .listReaction': {
        marginRight: theme.spacing(1.5)
      }
    }),
  ...(size &&
    size === 'sm' && {
      '& .itemReaction': {
        width: 16,
        height: 16,
        '& + .itemReaction': {
          marginLeft: '-3px'
        }
      },
      '& .listReaction': {
        marginRight: theme.spacing(0.25)
      }
    })
}));

const ListUser = ({ iconIdentity, itemIdentity, onUpdate }) => {
  const react_id =
    String(iconIdentity).split('.')[String(iconIdentity).split('.').length - 1];

  const [itemList, setItemList] = useState([]);
  const [leftUser, setLeftUser] = useState(0);
  const [loading, setLoading] = useState(false);

  const { dispatch, i18n } = useGlobal();
  const item = useSelector((state: GlobalState) =>
    getItemSelector(state, itemIdentity)
  );
  const react = useSelector<GlobalState, ReactionItem>((state: GlobalState) =>
    getReactionItemSelector(state, react_id)
  );

  const { item_type, item_id, resource_name, like_item_id, like_type_id, id } =
    item || {};

  useEffect(() => {
    let mounted = true;
    setLoading(true);
    // TODO: update item_type when move to api v5
    dispatch({
      type: 'react/getReactedListUser',
      payload: {
        id,
        item_id,
        item_type,
        resource_name,
        like_type_id,
        like_item_id,
        react_id,
        limit: 20
      },
      meta: {
        onSuccess: ({ data, extra }) => {
          if (!mounted) return;

          onUpdate(Math.random());
          setLoading(false);
          setItemList(data);
          setLeftUser(extra?.left);
        }
      }
    });

    return () => {
      mounted = false;
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [dispatch, item_id, item_type, onUpdate, react_id, resource_name]);

  if (loading) return <span>{i18n.formatMessage({ id: 'loading_dots' })}</span>;

  return (
    <div>
      <div>{react?.title}</div>
      {!!itemList.length &&
        itemList.map((item, index) => (
          <div key={index.toString()}>{item.user.full_name}</div>
        ))}
      {!!leftUser && (
        <div>
          {i18n.formatMessage(
            { id: 'and_value_more_dots' },
            { value: leftUser }
          )}
        </div>
      )}
    </div>
  );
};

export default function ReactionResult({
  data,
  handleAction,
  limit = 3,
  total,
  size = 'md',
  identity
}: MostReactionsProps) {
  const { dispatch } = useGlobal();
  const classes = useStyles();
  const [, shouldUpdate] = useState(0);

  if (!data || !Array.isArray(data) || !data.length) {
    return null;
  }

  const onClickReactIcon = (identity: string, reactIdentity?: string) => {
    dispatch({
      type: 'showPeopleWhoReactThisItem',
      payload: { identity, reactIdentity }
    });
  };

  return (
    <Wrapper data-testid="reactionResult" size={size} role="button">
      <div className={classes.listReaction}>
        {data.slice(0, limit).map((iconIdentity, index) => (
          <Tooltip
            disableInteractive
            enterDelay={1000}
            title={
              <ListUser
                iconIdentity={iconIdentity}
                itemIdentity={identity}
                onUpdate={shouldUpdate}
              />
            }
            key={index.toString()}
          >
            <ItemReaction
              className={'itemReaction'}
              onClick={() => onClickReactIcon(identity, iconIdentity)}
            >
              <ReactionIcon
                identity={iconIdentity}
                classes={classes}
                key={iconIdentity}
              />
            </ItemReaction>
          </Tooltip>
        ))}
      </div>
      {total ? (
        <Tooltip
          disableInteractive
          title={
            <ListUser
              iconIdentity={0}
              itemIdentity={identity}
              onUpdate={shouldUpdate}
            />
          }
        >
          <span
            className={classes.totalReaction}
            onClick={() => onClickReactIcon(identity)}
          >
            {total}
          </span>
        </Tooltip>
      ) : null}
    </Wrapper>
  );
}
