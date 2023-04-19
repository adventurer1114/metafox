import { useGlobal } from '@metafox/framework';
import { LineIcon } from '@metafox/ui';
import { Box, InputBase, styled } from '@mui/material';
import clsx from 'clsx';
import { debounce } from 'lodash';
import React, { memo, useCallback, useEffect, useMemo, useRef } from 'react';
import useStyles from './SearchFilter.styles';

const Root = styled('div')(({ theme }) => ({}));

const FilterOptionStyled = styled('div')(({ theme }) => ({
  display: 'inline-flex'
}));

const InputBaseStyled = styled(InputBase)(({ theme }) => ({
  display: 'flex',
  width: '100%',
  '&.Mui-focused': {
    borderColor: theme.palette.primary.main
  }
}));

const SearchWrapper = styled(Box, {
  name: 'SearchBox',
  slot: 'Root'
})(({ theme }) => ({
  margin: theme.spacing(0, 2, 1, 2),
  height: theme.spacing(5),
  borderRadius: theme.spacing(2.5),
  display: 'flex',
  alignItems: 'center',
  backgroundColor: theme.palette.action.hover
}));

export interface FilterOption {
  checked?: boolean;
  icon: string;
  value: string;
  id: string;
  label: string;
}
interface Props {
  roomId: string;
  placeholder: string;
  size: number;
  hide?: boolean;
  options?: FilterOption[];
  searching?: boolean;
}

type ClassKey = 'optionLabel' | 'optionChecked' | 'optionInput' | 'optionIcon';

interface FilterOptionProps {
  id: string;
  checked?: boolean;
  icon: string;
  classes: Record<ClassKey, string>;
  value: string;
  roomId: string;
  onClick: (value: string) => void;
}

export function SearchFilterOption({
  id,
  checked,
  icon,
  classes,
  value,
  roomId,
  onClick
}: FilterOptionProps) {
  const { dispatch } = useGlobal();

  const handleClick = () => {
    dispatch({ type: value, payload: { identity: roomId } });
    typeof onClick === 'function' && onClick(!checked ? id : undefined);
  };

  return (
    <label
      className={clsx(classes.optionLabel, checked && classes.optionChecked)}
    >
      <input className={classes.optionInput} type="checkbox" />
      <LineIcon
        icon={icon}
        className={classes.optionIcon}
        onClick={handleClick}
      />
    </label>
  );
}

const Filter = ({
  placeholder,
  size,
  hide,
  options = [],
  roomId,
  searching = false
}: Props) => {
  const classes = useStyles();
  const { dispatch } = useGlobal();
  const text = useRef<string>(null);
  const filter = useRef<string>(null);

  useEffect(() => {
    text.current = '';
    filter.current = null;
    debounced();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [searching]);

  const handleChange: React.ChangeEventHandler<HTMLInputElement> = e => {
    text.current = e.target.value;
    debounced();
  };

  const handleClickFilter = (value: string) => {
    filter.current = value;
    debounced();
  };

  const handleFilter = useCallback(() => {
    dispatch({
      type: 'chat/room/searchMessages',
      payload: { roomId, text: text.current }
    });
  }, [dispatch, roomId]);

  const debounced = useMemo(() => {
    return debounce(handleFilter, 200);
  }, [handleFilter]);

  useEffect(() => {
    if (!options.length) return;

    filter.current = options.find(item => item.checked)?.id;
  }, [options]);

  if (hide) return null;

  return (
    <Root>
      <SearchWrapper>
        <InputBaseStyled
          variant="search"
          onChange={handleChange}
          placeholder={placeholder}
          startAdornment={<LineIcon icon="ico-search-o" />}
          aria-controls="search_complete"
          endAdornment={
            <Box>
              {options && options.length ? (
                <FilterOptionStyled>
                  {options.map(option => (
                    <SearchFilterOption
                      id={option.id}
                      key={option.id.toString()}
                      checked={option?.checked}
                      classes={classes}
                      icon={option.icon}
                      value={option.value}
                      roomId={roomId}
                      onClick={handleClickFilter}
                    />
                  ))}
                </FilterOptionStyled>
              ) : null}
            </Box>
          }
        />
      </SearchWrapper>
    </Root>
  );
};

export default memo(Filter);
