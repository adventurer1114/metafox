import { useGlobal } from '@metafox/framework';
import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { ScrollContainer } from '@metafox/layout';
import { DialogSearchInput } from '@metafox/ui';
import { Box, Tab as MuiTab, Tabs } from '@mui/material';
import { styled } from '@mui/material/styles';
import { debounce } from 'lodash';
import React from 'react';
import { GROUPS_TAB, PAGES_TAB, USERS_TAB } from '../../constant';

const Tab = styled(MuiTab)(({ theme }) => ({
  height: 50,
  fontSize: theme.mixins.pxToRem(15),
  color: theme.palette.text.secondary,
  fontWeight: 'bold',
  borderBottom: '2px solid transparent',
  minWidth: 0,
  padding: 0,
  marginRight: theme.spacing(4),
  textTransform: 'uppercase',
  '& .Mui-selected': {
    borderBottomColor: theme.palette.primary.main,
    color: theme.palette.primary.main
  },
  '&:hover': {
    color: theme.palette.primary.main
  }
}));

const getType = tab => {
  switch (tab) {
    case USERS_TAB:
      return 'friend';
    case PAGES_TAB:
      return 'page';
    case GROUPS_TAB:
      return 'group';
  }
};

export default function ManageHidden(props) {
  const { i18n, useSession, useDialog, ListView } = useGlobal();
  const { dialogProps } = useDialog();
  const {
    user: { id: user_id }
  } = useSession();
  const [searchText, setText] = React.useState<string>('');
  const queryRef = React.useRef<string>('');
  const [tab, setTab] = React.useState<number>(USERS_TAB);

  const sourceData = React.useMemo(() => {
    return {
      source: {
        apiUrl: '/feed/hidden',
        apiParams: {
          user_id,
          limit: 6,
          q: searchText || undefined,
          type: getType(tab)
        }
      },
      paging: `/manage-hidden/tab=${tab}/${searchText}`
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [searchText, tab]);

  const Search = () => {
    if (queryRef.current.trim() !== searchText.trim()) {
      setText(queryRef.current.trim());
    }
  };

  const debounceSearch = debounce(Search, 1000);

  const handleChanged = value => {
    queryRef.current = value;
    debounceSearch();
  };

  const changeTab = (event, value) => {
    setTab(value);
  };

  return (
    <Dialog {...dialogProps} maxWidth="sm" fullWidth>
      <DialogTitle>{i18n.formatMessage({ id: 'manage_hidden' })}</DialogTitle>
      <DialogContent variant="fitScroll" sx={{ height: '50vh' }}>
        <Box sx={{ m: [2, 2, 0, 2] }}>
          <DialogSearchInput
            placeholder={i18n.formatMessage({
              id: 'search_users_pages_groups'
            })}
            onChanged={handleChanged}
          />
        </Box>
        <Tabs
          value={tab}
          onChange={changeTab}
          indicatorColor="primary"
          textColor="primary"
          sx={{ ml: 2 }}
        >
          <Tab disableRipple label={i18n.formatMessage({ id: 'users' })} />
          <Tab disableRipple label={i18n.formatMessage({ id: 'pages' })} />
          <Tab disableRipple label={i18n.formatMessage({ id: 'groups' })} />
        </Tabs>
        <div key={searchText}>
          <ScrollContainer autoHide autoHeight autoHeightMax={352}>
            <ListView
              canLoadMore
              dataSource={sourceData.source}
              pagingId={sourceData.paging}
              itemView={'user.itemView.hiddenUser'}
              emptyPage="core.block.no_content"
              emptyPageProps={{ title: `no_${getType(tab)}_found` }}
              clearDataOnUnMount
              prefixPagingId="/manage-hidden"
              gridLayout="Friend - Small List"
              itemLayout="Friend - Small List"
            />
          </ScrollContainer>
        </div>
      </DialogContent>
    </Dialog>
  );
}
