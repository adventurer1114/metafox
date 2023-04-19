/**
 * @type: service
 * name: SortCommentList
 */
import { LineIcon } from '@metafox/ui';
import {
  Box,
  Button,
  MenuItem,
  Popover,
  Tooltip,
  Typography
} from '@mui/material';
import React from 'react';
import { useGlobal } from '@metafox/framework';

export interface SortControlProps {
  label?: string;
  disabled?: boolean;
  value?: any;
  item?: any;
  setValue?: (value: string) => void;
}

enum Sort {
  Newest = 'newest',
  Oldest = 'oldest',
  All = 'all',
  Relevant = 'relevant'
}

const optionsDefault = [
  {
    label: 'all_comments',
    value: Sort.All,
    desc: 'all_comments_description'
  },
  {
    label: 'oldest',
    value: Sort.Oldest,
    desc: 'oldest_description'
  },
  {
    label: 'newest',
    value: Sort.Newest,
    desc: 'newest_description'
  }
];

const optionsDetail = [
  {
    label: 'most_relevant',
    value: Sort.Relevant,
    desc: 'most_relevant_description'
  },
  {
    label: 'all_comments',
    value: Sort.All,
    desc: 'all_comments_description'
  },
  {
    label: 'oldest',
    value: Sort.Oldest,
    desc: 'oldest_description'
  },
  {
    label: 'newest',
    value: Sort.Newest,
    desc: 'newest_description'
  }
];

function SortControl({ value, item, setValue, disabled }: SortControlProps) {
  const { i18n, usePageParams } = useGlobal();
  const { comment_id } = usePageParams();
  const options = comment_id ? optionsDetail : optionsDefault;
  const anchorRef = React.useRef<HTMLButtonElement>();
  const [open, setOpen] = React.useState<boolean>(false);
  const [sortType, setSortType] = React.useState(
    options.find(x => x.value === (value as Sort)) || options[0]
  );

  React.useEffect(() => {
    const opt = options.find(x => x.value === (value as Sort));

    if (opt) {
      setSortType(opt);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [value]);

  const handleClose = () => {
    setOpen(false);
  };

  const handleClick = () => {
    if (!disabled) setOpen(true);
  };

  const handleClickMenu = (item: any) => {
    handleClose();

    setValue(item.value);
  };

  return (
    <Box>
      <Tooltip title={item?.tooltip ? item?.tooltip : ''}>
        <Button
          size="smaller"
          variant="text"
          ref={anchorRef}
          onClick={handleClick}
          disabled={disabled}
          data-testid={'buttonSortComment'}
          disableRipple
          sx={{ background: 'transparent !important', padding: 0 }}
        >
          <Typography
            fontWeight="fontWeightBold"
            variant="body2"
            color="text.secondary"
            sx={{
              display: 'flex',
              alignItems: 'center',
              '> span': { marginLeft: '4px' }
            }}
          >
            {i18n.formatMessage({ id: 'sort_comment' })}:{' '}
            {i18n.formatMessage({ id: sortType.label })}
            {!disabled && <LineIcon icon="ico-caret-down" />}
          </Typography>
        </Button>
      </Tooltip>
      <Popover
        disablePortal
        id={open ? 'sortType-popover' : undefined}
        open={Boolean(open)}
        anchorEl={anchorRef.current}
        onClose={handleClose}
        anchorOrigin={{ vertical: 'bottom', horizontal: 'left' }}
        transformOrigin={{ vertical: 'top', horizontal: 'left' }}
      >
        <Box py={1} component="div" data-testid="menusortType">
          {options
            ? options.map((item, index) => (
                <MenuItem
                  value={item.value}
                  data-value={item.value}
                  key={index.toString()}
                  onClick={() => handleClickMenu(item)}
                >
                  <Box sx={{ display: 'flex', flexDirection: 'column' }}>
                    <Typography
                      sx={{ marginBottom: '4px' }}
                      variant="body1"
                      fontWeight="fontWeightSemiBold"
                    >
                      {i18n.formatMessage({ id: item.label })}
                    </Typography>
                    <Typography variant="body2" color="text.secondary">
                      {i18n.formatMessage({ id: item.desc })}
                    </Typography>
                  </Box>
                </MenuItem>
              ))
            : null}
        </Box>
      </Popover>
    </Box>
  );
}

export default SortControl;
