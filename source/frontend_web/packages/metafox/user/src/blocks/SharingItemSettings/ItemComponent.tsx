import { SharingPrivacyItem } from '../../types';
import * as React from 'react';
import {
  ListItem,
  ListItemText,
  Select,
  MenuItem,
  ListItemSecondaryAction
} from '@mui/material';
import useStyles from './styles';

export type Props = {
  item: SharingPrivacyItem;
  onChanged: (value: number, var_name: string) => void;
  classes: any;
};

export default function ItemComponent({ item, onChanged }: Props) {
  const classes = useStyles();
  const [value, setValue] = React.useState<string>(item.value.toString());

  const handleChange = React.useCallback(
    (evt: React.ChangeEvent<{ value: unknown }>) => {
      const newValue = evt.target.value as string;
      setValue(newValue);
      onChanged(parseInt(newValue, 10), item.var_name);
    },
    [item.var_name, onChanged]
  );

  return (
    <ListItem className={classes.listItem}>
      <ListItemText primary={item.phrase} />
      <ListItemSecondaryAction>
        <Select
          onChange={handleChange}
          variant={'standard'}
          value={value}
          disableUnderline
        >
          {item.options.map(option => (
            <MenuItem
              key={option.value.toString()}
              value={option.value.toString()}
            >
              {option.label}
            </MenuItem>
          ))}
        </Select>
      </ListItemSecondaryAction>
    </ListItem>
  );
}
