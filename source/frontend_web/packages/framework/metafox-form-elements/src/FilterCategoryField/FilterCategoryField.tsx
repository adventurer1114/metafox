/**
 * @type: formElement
 * name: form.element.FilterCategory
 * chunkName: formElement
 */
import { useGlobal } from '@metafox/framework';
import { FormFieldProps } from '@metafox/form/types';
import { Box, Divider, Typography } from '@mui/material';
import { useField } from 'formik';
import { camelCase, get } from 'lodash';
import * as React from 'react';
import ItemView from './ItemView';
import useStyles from './styles';

export default function FilterCategoryField({
  config: { label, apiUrl, dataSource = [], defaultValue },
  name
}: FormFieldProps) {
  const classes = useStyles();
  const [, , { setValue }] = useField(name);
  const { useFetchItems, usePageParams, i18n } = useGlobal();
  const pageParams = usePageParams();
  const category_id = get(pageParams, name);

  const [items] = useFetchItems({
    dataSource: {
      apiUrl
    },
    data: [],
    cache: true
  });

  const menus = items.length > 0 ? items : dataSource;

  const handleSelect = React.useCallback(
    (id: string) => {
      setValue(id);
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

  const handleReset = React.useCallback(() => {
    setValue(undefined);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);

  return (
    <div data-testid={camelCase(`field ${name}`)}>
      <Box sx={{ pt: 2, pb: 2 }}>
        <Divider />
      </Box>
      <Typography
        component="h4"
        variant="h4"
        color="textPrimary"
        sx={{ pb: 1 }}
      >
        {label}
      </Typography>
      <Box sx={{ pb: 3 }}>
        {menus && menus.length ? (
          <ItemView
            classes={classes}
            item={{ name: i18n.formatMessage({ id: 'all' }) }}
            handleSelect={handleReset}
            key={'all'}
            active={!category_id}
            category_id={category_id}
          />
        ) : null}
        {menus.map(item => (
          <ItemView
            classes={classes}
            item={item}
            handleSelect={handleSelect}
            key={item.id}
            active={category_id === String(item.id)}
            category_id={category_id}
          />
        ))}
      </Box>
    </div>
  );
}
