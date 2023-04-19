import { Checkbox, FormControlLabel } from '@mui/material';
import React from 'react';
import useStyles from './styles';

export default function CollectionItem(props) {
  const { item, handleToggleItem, checked: checkedProp, loading } = props;
  const classes = useStyles();
  const [checked, setChecked] = React.useState(checkedProp);

  const handleCheckboxChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    handleToggleItem(item?.id, !checked);
    setChecked(prev => !prev);
  };

  React.useEffect(() => {
    setChecked(checkedProp);
  }, [checkedProp]);

  if (!item) return null;

  return (
    <FormControlLabel
      className={classes.itemWrapper}
      control={
        <Checkbox
          disabled={loading}
          checked={checked}
          onChange={handleCheckboxChange}
          color="primary"
          size="small"
        />
      }
      label={item.name}
    />
  );
}
