/**
 * @type: formElement
 * name: form.element.SimpleCategory
 * chunkName: formExtras
 */
import { FormFieldProps } from '@metafox/form/types';
import { Box, Divider, Typography } from '@mui/material';
import { useField } from 'formik';
import { camelCase } from 'lodash';
import * as React from 'react';
import ItemView from './ItemView';
import useStyles from './styles';

export default function SimpleCategory({
  config: { label, dataSource = [], defaultValue },
  name
}: FormFieldProps) {
  const classes = useStyles();
  const [field, , { setValue }] = useField(name);

  const menus = dataSource;

  const handleSelect = React.useCallback(
    (id: string) => {
      setValue(id);
    },
    // eslint-disable-next-line react-hooks/exhaustive-deps
    []
  );

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
      <div>
        {menus.map(item => (
          <ItemView
            classes={classes}
            item={item}
            handleSelect={handleSelect}
            key={item.id}
            active={item.id === field.value}
          />
        ))}
      </div>
    </div>
  );
}
