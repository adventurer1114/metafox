import { Link, useGlobal } from '@metafox/framework';
import { DotSeparator } from '@metafox/ui';
import { SxProps } from '@mui/material';
import React from 'react';

interface Item {
  name: string;
  id: number;
}

interface CategoryListProps<T extends Item = Item> {
  to: string;
  data: T[];
  className?: string;
  sx?: SxProps;
  isTruncateOneLine?: boolean;
  displayLimit?: number;
}

export function CategoryList({
  data,
  to,
  sx,
  isTruncateOneLine = false,
  displayLimit,
  ...props
}: CategoryListProps) {
  const { i18n } = useGlobal();
  const [categoryList, setCategoryList] = React.useState(
    data?.length ? data : []
  );

  React.useEffect(() => {
    if (displayLimit && data) {
      const dataTmp: any[] = data.slice(0, displayLimit);

      if (data.length > displayLimit) {
        dataTmp.push({
          id: 0,
          name: i18n.formatMessage(
            { id: 'total_more_categories' },
            {
              total: data.length - displayLimit
            }
          )
        });
      }

      setCategoryList(dataTmp);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [displayLimit, data]);

  if (!data?.length) return null;

  const handleShowMore = () => {
    setCategoryList(data);
  };

  return (
    <DotSeparator
      sx={{
        textTransform: 'uppercase',
        fontSize: '13px',
        display: 'flex !important',
        flexWrap: 'wrap',
        lineHeight: 1,
        color: 'primary.main',
        ...sx
      }}
      isTruncateOneLine={isTruncateOneLine}
      {...props}
    >
      {categoryList.map((item, index) => {
        if (item.id === 0) {
          return (
            <Link key={index} onClick={handleShowMore}>
              {item.name}
            </Link>
          );
        }

        return (
          <Link key={index.toString()} to={`${to}/${item.id}`}>
            {item.name}
          </Link>
        );
      })}
    </DotSeparator>
  );
}
