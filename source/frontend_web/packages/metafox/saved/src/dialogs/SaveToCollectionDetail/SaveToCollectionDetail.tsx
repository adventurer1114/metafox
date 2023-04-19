/**
 * @type: dialog
 * name: saved.dialog.saveToCollectionDetail
 */

import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { useGlobal } from '@metafox/framework';
import { ScrollContainer } from '@metafox/layout';
import { Box, CircularProgress } from '@mui/material';
import { isEmpty } from 'lodash';
import React from 'react';
import CollectionItem from '../../components/MenuItem/AddToCollection/CollectionItem';
import NoCollectionList from './NoCollectionList';

interface Props {
  item: any;
  noShowFeedback?: boolean;
}

const LoadingComponent = () => (
  <Box my={1} sx={{ display: 'flex', justifyContent: 'center' }}>
    <CircularProgress color="inherit" size="1rem" />
  </Box>
);

export default function SaveToCollectionDetail({
  item,
  noShowFeedback
}: Props) {
  const { useDialog, i18n, useFetchItems, dispatch } = useGlobal();
  const dialogItem = useDialog();
  const { dialogProps } = dialogItem;

  const identity = item._identity;
  const { collection_ids = [] } = item || {};

  const [checkedList, setCheckedList] = React.useState(collection_ids || []);
  const [submitting, setSubmitting] = React.useState<boolean>(false);

  const [collections, loading] = useFetchItems({
    dataSource: {
      apiUrl: '/saveditems-collection'
    }
  });

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
        isRemoved: !isChecked,
        noShowFeedback
      },
      meta: {
        onSuccess: () => {
          setSubmitting(false);
          setCheckedList(data);
        }
      }
    });
  };

  const Content = () => {
    if (isEmpty(collections)) {
      return (
        <NoCollectionList
          icon="ico-list-bullet-o"
          description="no_collections_found"
          button={{
            type: 'addSavedItemToNewCollection',
            props: {
              identity
            },
            label: 'new_collection'
          }}
        />
      );
    }

    return (
      <Box sx={{ p: 1 }}>
        <ScrollContainer autoHide autoHeight autoHeightMax={300}>
          {collections.map(item => (
            <CollectionItem
              item={item}
              collection_ids={collection_ids}
              key={item.id.toString()}
              handleToggleItem={handleToggleItem}
              checked={checkedList.some(x => x === item.id)}
              loading={submitting}
            />
          ))}
        </ScrollContainer>
      </Box>
    );
  };

  return (
    <Dialog {...dialogProps} maxWidth="xs" data-testid="">
      <DialogTitle
        children={i18n.formatMessage({ id: 'save_to_collection' })}
        data-testid="popupTitle"
      />
      <DialogContent sx={{ maxWidth: '100%', p: 0 }}>
        {loading ? <LoadingComponent /> : <Content />}
      </DialogContent>
    </Dialog>
  );
}
