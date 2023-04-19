/**
 * @type: service
 * name: CommentList
 */
import { getItemSelector, GlobalState, useGlobal } from '@metafox/framework';
import { ItemShape } from '@metafox/ui';
import { styled, Box, CircularProgress } from '@mui/material';
import React from 'react';
import { useSelector } from 'react-redux';
import { CommentListProps } from '../../types';
import PreFetchComment from '../Comment/PreFetchComment';
import {
  SORT_ALL,
  SORT_MODE_ASC,
  SortTypeValue,
  SORT_RELEVANT
} from '@metafox/comment';
import { isEmpty, uniqBy } from 'lodash';
import {
  getValueSortTypeMode,
  getDataBySortTypeMode
} from '@metafox/comment/utils';

const CommentListRoot = styled('div', { name: 'CommentListRoot' })(
  ({ theme }) => ({
    position: 'relative',
    '& $CommentRoot:first-of-type': {
      marginTop: theme.spacing(1)
    },
    '& $CommentRoot:last-child': {
      borderBottom: 'solid 1px',
      borderBottomColor: theme.palette.border?.secondary,
      paddingBottom: theme.spacing(2),
      [theme.breakpoints.down('sm')]: {
        borderBottom: 'none'
      }
    }
  })
);

const CommentRoot = styled('div', { name: 'CommentListRoot' })(({ theme }) => ({
  position: 'relative',
  '&:before': {
    content: '""',
    position: 'absolute',
    top: theme.spacing(6),
    bottom: theme.spacing(2),
    left: theme.spacing(2),
    borderLeft: 'solid 1px',
    borderLeftColor: theme.palette.border?.secondary
  }
}));

const ViewMoreComment = styled(Box, { name: 'viewMoreComment' })(
  ({ theme }) => ({
    color: theme.palette.primary.main,
    '&:hover': {
      textDecoration: 'underline'
    }
  })
);

const compareDate = (a, b, sortKeySetting) => {
  return (
    new Date(a[sortKeySetting]).valueOf() -
    new Date(b[sortKeySetting]).valueOf()
  );
};

export default function CommentList({
  identity,
  data: dataDefault,
  open = true,
  total_comment: total_comment_all,
  total_reply = 0,
  total_hidden = 0,
  viewMoreComments,
  parent_user,
  sortType,
  setSortType,
  setShowSort,
  forceHideSort = false,
  handleAction,
  setLoadingSort: setLoadingSortParent
}: CommentListProps) {
  const total_comment = total_comment_all - total_hidden;
  const {
    CommentItemView,
    i18n,
    getSetting,
    useGetItem,
    useGetItems,
    SortCommentList
  } = useGlobal();
  const sortTypeSetting: SortTypeValue = getSetting('comment.sort_by');
  const sortKeySetting = getSetting('comment.sort_by_key') || 'creation_date';
  const [loadingSort, setLoadingSort] = React.useState(false);
  const [loadingMore, setLoadingMore] = React.useState(false);
  const sortTypeMode = getValueSortTypeMode(sortType);
  const sortTypeModeSetting = getValueSortTypeMode(sortTypeSetting);
  const pagingId = `comment/${sortTypeMode}/${identity.replace(/\./g, '_')}`;
  const pagingData = useGetItem(`pagination.${pagingId}`);
  const endedMore = pagingData?.ended;

  const item = useSelector<GlobalState>(state =>
    getItemSelector(state, identity)
  ) as ItemShape & {
    preFetchingComment: Record<string, any>;
    excludesComment: string[];
    commentsNew: string[];
    relevant_comments: string[];
  };
  const {
    preFetchingComment,
    commentsNew = [],
    excludesComment = [],
    relevant_comments = []
  } = item || {};

  const dataSortList = getDataBySortTypeMode(item, sortTypeMode);
  const data =
    !isEmpty(dataSortList) ||
    (!isEmpty(relevant_comments) && SORT_RELEVANT === sortType)
      ? dataSortList || []
      : dataDefault || [];

  const dataCommentsContain = useGetItems(data);
  const dataCommentsNew = useGetItems(commentsNew);
  const dataCommentsRelevant = useGetItems(
    SORT_RELEVANT === sortType ? relevant_comments : []
  );
  const dataComments = [...dataCommentsNew, ...dataCommentsContain];
  const dataSort = uniqBy(
    sortTypeMode === SORT_MODE_ASC
      ? dataCommentsNew
          .sort((a, b) => compareDate(b, a, sortKeySetting))
          .concat(dataCommentsRelevant)
          .concat(dataCommentsContain)
      : dataComments.sort((a, b) =>
          (sortType === SORT_ALL
            ? compareDate(a, b, sortKeySetting)
            : compareDate(b, a, sortKeySetting))
        ),
    'id'
  );
  const countCommentShowed = dataSort ? dataSort.length : 0;
  const remainComment = Math.max(
    Math.min(10, total_comment - total_reply - countCommentShowed),
    0
  );

  const isShowSort = dataSort.length > 1 || !!remainComment;

  React.useEffect(() => {
    if (setShowSort) {
      setShowSort(isShowSort);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isShowSort]);

  React.useEffect(() => {
    setLoadingMore(false);
  }, [dataComments.length]);

  React.useEffect(() => {
    if (sortTypeMode !== sortTypeModeSetting && sortType !== SORT_RELEVANT) {
      setLoadingSort(true);
      handleAction(
        'comment/changeSort',
        { sortType, excludes: excludesComment },
        { onSuccess: () => setLoadingSort(false) }
      );
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [sortTypeMode]);

  React.useEffect(() => {
    if (setLoadingSortParent) {
      setLoadingSortParent(loadingSort);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [loadingSort]);

  if (!open) return null;

  if (total_comment === 0 && !preFetchingComment) return null;

  const hasComment = dataSort && dataSort.length;
  const showViewMore = 0 < remainComment && !endedMore;

  const handleClickMore = () => {
    setLoadingMore(true);
    viewMoreComments(
      {
        sortType,
        sortSettingDefault: sortTypeSetting,
        pagingId
      },
      {
        loadmoreFinish: () => {
          setLoadingMore(false);
        }
      }
    );
  };

  if (loadingSort) {
    return (
      <CommentListRoot>
        {!forceHideSort && isShowSort ? (
          <Box pt={2}>
            <SortCommentList value={sortType} setValue={setSortType} />
          </Box>
        ) : null}
        <Box
          p={2}
          sx={{
            display: 'flex',
            alignItems: 'center',
            justifyContent: 'center'
          }}
        >
          <CircularProgress size={16} />
        </Box>
      </CommentListRoot>
    );
  }

  return (
    <CommentListRoot>
      {!forceHideSort && isShowSort ? (
        <Box pt={2}>
          <SortCommentList value={sortType} setValue={setSortType} />
        </Box>
      ) : null}
      {sortType === SORT_ALL ? (
        <>
          {showViewMore && (
            <ViewMoreComment mt={2}>
              <Box
                sx={{ display: 'flex', alignItems: 'center' }}
                role="button"
                onClick={handleClickMore}
              >
                {i18n.formatMessage(
                  { id: 'view_previous_comment' },
                  { value: remainComment }
                )}
                {loadingMore && (
                  <CircularProgress sx={{ marginLeft: '4px' }} size={12} />
                )}
              </Box>
            </ViewMoreComment>
          )}
          {hasComment
            ? dataSort.map(item => (
                <CommentRoot key={item?.id}>
                  <CommentItemView
                    identity={`comment.entities.comment.${item?.id}`}
                    identityResource={identity}
                    parent_user={parent_user}
                  />
                </CommentRoot>
              ))
            : null}
          {preFetchingComment
            ? Object.values(preFetchingComment)
                .filter(item => item?.isLoading === true)
                .map(item => (
                  <CommentRoot key={item}>
                    <PreFetchComment text={item.text} />
                  </CommentRoot>
                ))
            : null}
        </>
      ) : (
        <>
          {preFetchingComment
            ? Object.values(preFetchingComment)
                .filter(item => item?.isLoading === true)
                .reverse()
                .map(item => (
                  <CommentRoot key={item}>
                    <PreFetchComment text={item.text} />
                  </CommentRoot>
                ))
            : null}
          {hasComment
            ? dataSort.map(item => (
                <CommentRoot key={item?.id}>
                  <CommentItemView
                    identity={`comment.entities.comment.${item?.id}`}
                    identityResource={identity}
                    parent_user={parent_user}
                  />
                </CommentRoot>
              ))
            : null}
          {showViewMore && (
            <ViewMoreComment py={2}>
              <Box
                sx={{ display: 'flex', alignItems: 'center' }}
                role="button"
                onClick={handleClickMore}
              >
                {i18n.formatMessage(
                  { id: 'view_more_comment' },
                  { value: remainComment }
                )}
                {loadingMore && (
                  <CircularProgress sx={{ marginLeft: '4px' }} size={12} />
                )}
              </Box>
            </ViewMoreComment>
          )}
        </>
      )}
    </CommentListRoot>
  );
}
