import * as React from 'react';
import {
  ListItem,
  ListItemText,
  Select,
  MenuItem,
  styled
} from '@mui/material';
import { isBoolean } from 'lodash';

const StyledListItem = styled(ListItem, { name: 'StyledListItem' })(
  ({ theme }) => ({
    borderBottom: 'solid 1px',
    borderBottomColor: theme.palette.border?.secondary,
    padding: '22px 0 !important',
    display: 'flex',
    justifyContent: 'space-between',
    '&:first-of-typed': {
      paddingTop: 6
    },
    '&:last-child': {
      paddingBottom: 6,
      borderBottom: 'none'
    }
  })
);

export type Props = {
  item: Record<string, any>;
  onChanged: (value: number | boolean, var_name: string) => void;
};

export function SelectItem({ item, onChanged }: Props) {
  const [value, setValue] = React.useState<string>(item?.value?.toString());

  const handleChange = (evt: React.ChangeEvent<{ value: unknown }>) => {
    const newValue = evt.target.value as string;

    setValue(newValue);
    onChanged(
      isBoolean(newValue) ? newValue : parseInt(newValue, 10),
      item?.var_name
    );
  };

  return (
    <StyledListItem>
      <ListItemText primary={item.phrase} sx={{ pr: 2 }} />
      <Select
        variant={'standard'}
        value={value}
        onChange={handleChange}
        disableUnderline
      >
        {item?.options?.map((option, index) => (
          <MenuItem
            value={
              isBoolean(option.value) ? option.value : option.value.toString()
            }
            key={index}
          >
            {option.label}
          </MenuItem>
        ))}
      </Select>
    </StyledListItem>
  );
}
