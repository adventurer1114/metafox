import { useGlobal } from '@metafox/framework';
import { Autocomplete, Box, styled, TextField } from '@mui/material';
import React from 'react';
import { debounce, isArray } from 'lodash';
import { TruncateText } from '@metafox/ui';

const SearchForm = styled('div', {
  name: 'AdminSearchForm',
  slot: 'Root'
})(({ theme }) => ({
  float: 'right',
  position: 'relative',
  width: '160px',
  [theme.breakpoints.up('sm')]: {
    width: '320px'
  }
}));

interface OptionShape {
  label: string;
  caption: string;
  group: string;
  url: string;
}

const renderOption = (props, option: OptionShape) => {
  return (
    <li {...props}>
      <Box sx={{ overflow: 'hidden' }}>
        <TruncateText lines={1}>{option.label}</TruncateText>
        <Box>
          <TruncateText
            lines={1}
            color="text.secondary"
            variant="body2"
            fontSize={12}
            component="span"
          >
            {option.caption}
          </TruncateText>
        </Box>
      </Box>
    </li>
  );
};

export default function AdminSearchForm() {
  const { apiClient, i18n, navigate } = useGlobal();
  const [inputValue, setInputValue] = React.useState<string>('');
  const [options, setOptions] = React.useState<OptionShape[]>([]);
  const store = React.useRef<Record<string, OptionShape[]>>({});

  const fetch = (value: string) => {
    if (store.current[value]) {
      setOptions(store.current[value]);
    } else {
      apiClient.get(`admincp/core/search?q=${value}`).then(x => {
        const options = isArray(x?.data.data) ? x.data.data : [];
        store.current[value] = options;
        setOptions(options);
      });
    }
  };

  const handleChange = (evt, value) => {
    if (value?.url) {
      navigate(value.url);
    }
  };

  const handleFetch = React.useCallback(debounce(fetch, 500), []);

  const handleInputChange = React.useCallback((evt: any, value: string) => {
    if (evt?.type !== 'change') return;

    setInputValue(value);
    handleFetch(value);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <SearchForm data-testid="searchForm">
      <Autocomplete<OptionShape, false, true, true>
        size="small"
        id="admin_search_box"
        filterOptions={x => x}
        disableClearable
        noOptionsText={i18n.formatMessage({ id: 'no_results' })}
        onChange={handleChange}
        options={options}
        autoSelect
        inputValue={inputValue}
        onInputChange={(evt, newValue) => handleInputChange(evt, newValue)}
        sx={{ width: '100%' }}
        renderOption={renderOption}
        renderInput={params => {
          return (
            <TextField
              {...params}
              size="small"
              type="text"
              placeholder="Search"
              data-testid="searchBox"
            />
          );
        }}
      />
    </SearchForm>
  );
}
