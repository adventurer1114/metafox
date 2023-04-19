/**
 * @type: dialog
 * name: core.dialog.PlacePickerDialog
 */
import { Address, useGlobal, useSearchPlaces } from '@metafox/framework';
import { Dialog, DialogContent, DialogTitle } from '@metafox/dialog';
import { DialogSearchInput } from '@metafox/ui';
import { isEmpty } from 'lodash';
import React from 'react';
import ResultItem from './ResultItem';
import useStyles from './styles';

export type ClassesKeys =
  | 'searchIcon'
  | 'dialogContent'
  | 'dialogTitle'
  | 'mapStage'
  | 'dialog'
  | 'inputStage';

export type PlacePickerProps = {
  classes: Record<ClassesKeys, string>;
  inputControl: React.ElementType<any>;
  title: string;
  placeholder: string;
  defaultValue: Address;
};

function PlacePickerDialog({ defaultValue }: PlacePickerProps) {
  const classes = useStyles();
  const { useDialog, i18n } = useGlobal();
  const { dialogProps, setDialogValue } = useDialog();

  const title = i18n.formatMessage({ id: 'search_for_location' });
  const placeholder = i18n.formatMessage({ id: 'search_dots' });
  const mapRef = React.useRef<HTMLDivElement>();
  const [items, query, setQuery, currentItem] = useSearchPlaces(mapRef, {
    ...defaultValue,
    address: ''
  });

  const onChanged = React.useCallback(
    (value: string) => setQuery(value),
    [setQuery]
  );

  const handleSelect = (item: unknown) => {
    setDialogValue({ ...(item as any), query });
  };

  const handleReset = () => {
    setDialogValue(false);
  };

  return (
    <Dialog
      className={classes.dialog}
      maxWidth="sm"
      fullWidth
      data-testid="dialogLocationPicker"
      {...dialogProps}
    >
      <DialogTitle data-testid="popupTitle" enableBack disableClose>
        {title}
      </DialogTitle>
      <div className={classes.inputStage}>
        <DialogSearchInput
          autoFocus
          data-testid="searchBox"
          placeholder={placeholder}
          onChanged={onChanged}
        />
      </div>
      <DialogContent className={classes.dialogContent}>
        <div className={classes.dialogContentTitle}>
          {i18n.formatMessage({ id: 'base_on_your_location' })}
        </div>
        <div className={classes.mapStage} ref={mapRef} />
        {!isEmpty(currentItem) && !query && (
          <ResultItem
            active
            name={currentItem.name}
            address={currentItem.address}
            onClick={handleReset}
          />
        )}
        {!!items?.length &&
          items.map((item, index) => {
            if (item.lat === currentItem?.lat && item.lng === currentItem?.lng)
              return null;

            return (
              <ResultItem
                name={item.name}
                address={item.address}
                key={index.toString()}
                onClick={() => handleSelect(item)}
              />
            );
          })}
      </DialogContent>
    </Dialog>
  );
}

export default PlacePickerDialog;
