import { useGlobal } from '@metafox/framework';
import React from 'react';
import { SORT_RELEVANT, SortTypeValue } from '@metafox/comment';

export default function useSortComment() {
  const { getSetting, usePageParams } = useGlobal();
  const { comment_id } = usePageParams();
  const sortSetting: SortTypeValue = getSetting('comment.sort_by');
  const [sortType, setSortType] = React.useState<SortTypeValue>(
    comment_id ? SORT_RELEVANT : sortSetting
  );
  const [loading, setLoading] = React.useState(false);

  return [sortType, setSortType, loading, setLoading];
}
