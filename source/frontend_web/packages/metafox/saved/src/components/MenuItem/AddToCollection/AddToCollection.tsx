/**
 * @type: ui
 * name: menuItem.as.addToCollection
 */
import { getItemSelector, GlobalState, useGlobal } from '@metafox/framework';
import { ScrollContainer } from '@metafox/layout';
import { SavedItemShape } from '@metafox/saved/types';
import { ControlMenuItemProps, LineIcon } from '@metafox/ui';
import { Box, Button, CircularProgress } from '@mui/material';
import React from 'react';
import { useSelector } from 'react-redux';
import CollectionItem from './CollectionItem';
import useStyles from './styles';

type Props = {
  identity: string;
} & ControlMenuItemProps;

export default function AddToCollection(props: Props) {
  const { identity } = props;
  const classes = useStyles();
  const { i18n, useFetchItems, dispatch } = useGlobal();

  const savedItem = useSelector<GlobalState>(state =>
    getItemSelector(state, identity)
  ) as SavedItemShape;

  const { collection_ids } = savedItem || {};
  const [checkedList, setCheckedList] = React.useState(collection_ids || []);
  const [submitting, setSubmitting] = React.useState<boolean>(false);

  const [collections, loading] = useFetchItems({
    dataSource: {
      apiUrl: '/saveditems-collection'
    }
  });

  const handleAddCollection = () => {
    dispatch({
      type: 'addSavedItemToNewCollection',
      payload: { identity }
    });
  };

  const handleToggleItem = (idItem, isChecked) => {
    setSubmitting(true);

    const data = isChecked
      ? [...checkedList, idItem]
      : checkedList.filter(x => x !== idItem);

    dispatch({
      type: 'addSavedItemToCollection',
      payload: {
        identity,
        ids: data,
        collection_id: idItem,
        isRemoved: !isChecked
      },
      meta: {
        onSuccess: () => {
          setSubmitting(false);
          setCheckedList(data);
        }
      }
    });
  };

  return (
    <div className={classes.root}>
      <div className={classes.heading}>
        {i18n.formatMessage({ id: 'add_to_collection' })}
      </div>
      {loading ? (
        <Box my={1} sx={{ display: 'flex', justifyContent: 'center' }}>
          <CircularProgress color="inherit" size="1rem" />
        </Box>
      ) : (
        <div>
          {collections ? (
            <div className={classes.listCollection}>
              <ScrollContainer autoHide autoHeight autoHeightMax={198}>
                {collections.map(item => (
                  <CollectionItem
                    item={item}
                    identity={identity}
                    collection_ids={collection_ids}
                    key={item.id.toString()}
                    handleToggleItem={handleToggleItem}
                    checked={checkedList.some(x => x === item.id)}
                    loading={submitting}
                  />
                ))}
              </ScrollContainer>
            </div>
          ) : (
            <Box m={2}>
              {i18n.formatMessage({ id: 'no_collections_found' })}
            </Box>
          )}
        </div>
      )}
      <Box p={1}>
        <Button
          variant="contained"
          size="small"
          color="primary"
          fullWidth
          onClick={handleAddCollection}
        >
          <LineIcon icon="ico-plus" sx={{ mr: 1, fontSize: 15 }} />
          {i18n.formatMessage({ id: 'new_collection' })}
        </Button>
      </Box>
    </div>
  );
}
