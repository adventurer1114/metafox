import HtmlViewer from '@metafox/html-viewer';
import { ItemSummary, ItemTitle, ItemView, Statistic } from '@metafox/ui';
import React from 'react';
import LoadingSkeleton from './LoadingSkeleton';
import useStyles from './styles';

export default function TrendingHashtagItemView({
  item,
  identity,
  wrapAs,
  wrapProps
}) {
  const classes = useStyles();

  return (
    <ItemView
      wrapAs={wrapAs}
      wrapProps={wrapProps}
      testid={`${item.resource_name}`}
      data-eid={identity}
    >
      <ItemTitle className={classes.title}>
        <HtmlViewer html={item?.tag_hyperlink} />
      </ItemTitle>
      <ItemSummary>
        <Statistic
          values={item?.statistic}
          display={'total_post'}
          fontStyle={'minor'}
        />
      </ItemSummary>
    </ItemView>
  );
}

TrendingHashtagItemView.LoadingSkeleton = LoadingSkeleton;
