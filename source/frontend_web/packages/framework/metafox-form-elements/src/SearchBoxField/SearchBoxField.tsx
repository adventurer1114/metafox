/**
 * @type: formElement
 * name: form.element.SearchBox
 * chunkName: formElement
 */

import { FormFieldProps } from '@metafox/form/types';
import { useGlobal } from '@metafox/framework';
// components
import { LineIcon, SearchBox } from '@metafox/ui';
// hooks
import { FormControl, styled } from '@mui/material';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import React, { useEffect, useState } from 'react';

const IconClearStyled = styled(LineIcon, { name: 'IconClearStyled' })(
  ({ theme }) => ({
    marginRight: theme.spacing(0.5),
    cursor: 'pointer'
  })
);

export default function SearchBoxField({
  config: {
    placeholder,
    size,
    margin = 'normal',
    fullWidth = true,
    className,
    sx
  },
  name
}: FormFieldProps) {
  const { eventCenter } = useGlobal();
  const [field, , { setValue }] = useField(name);
  const [query, setQuery] = useState(field.value || '');
  const { usePageParams } = useGlobal();
  const pageParams = usePageParams();

  useEffect(() => {
    if (pageParams?.q) {
      setQuery(pageParams?.q);
      setValue(pageParams?.q);
    }
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [pageParams]);

  useEffect(() => {
    eventCenter.dispatch('minimizeGlobalSearchForm', true);
  });

  const handleKeyDown = React.useCallback(
    (evt: KeyboardEvent) => {
      if (evt.key === 'Enter') {
        // stop submit event when query string is empty
        if (query) {
          setValue(query.trim());
        } else {
          setValue('');
        }

        evt.preventDefault();
      }
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [query]
  );

  const clearValue = () => {
    setQuery('');
    setValue('');
  };

  return (
    <FormControl
      size={size}
      margin={margin}
      fullWidth={fullWidth}
      data-testid={camelCase(`field ${name}`)}
      sx={sx}
    >
      <SearchBox
        name="q"
        value={query}
        onChange={e => setQuery(e.target.value)}
        placeholder={placeholder}
        onKeyDown={handleKeyDown}
        endAdornment={
          query ? (
            <IconClearStyled icon="ico-close" onClick={clearValue} />
          ) : null
        }
      />
    </FormControl>
  );
}
