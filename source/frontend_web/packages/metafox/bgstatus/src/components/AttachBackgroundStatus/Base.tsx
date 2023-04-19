import { getBgStatusSelector } from '@metafox/bgstatus/selectors';
import { GlobalState, useGlobal } from '@metafox/framework';
import { styled, Tooltip } from '@mui/material';
import { isFunction } from 'lodash';
import React from 'react';
import { useSelector } from 'react-redux';
import {
  AppState,
  BgStatusItemShape,
  BgStatusListBaseProps
} from '../../types';

export type BgStatusListProps = BgStatusListBaseProps;

const name = 'AttachBackgroundStatus';

const size = 24;

const Wrapper = styled('div', { name, slot: 'root' })(({ theme }) => ({
  display: 'flex',
  flexWrap: 'wrap',
  padding: theme.spacing(0, 0.5)
}));

const ItemRoot = styled('div', { name, slot: 'root' })(({ theme }) => ({
  cursor: 'pointer',
  padding: theme.spacing(0.5),
  border: '1px solid transparent'
}));

const SelectBackgroundButton = styled('div', { name, slot: 'root' })(
  ({ theme }) => ({
    width: size,
    height: size,
    borderRadius: 4,
    display: 'flex',
    alignItems: 'center',
    justifyContent: 'center'
  })
);

const RemoveBackgroundButton = styled('div', { name, slot: 'root' })(
  ({ theme }) => ({
    width: size,
    height: size,
    borderRadius: 4,
    border: '1px solid rgba(0,0,0,0.2)',
    backgroundColor: theme.mixins.backgroundColor('paper')
  })
);

const SelectBackgroundIcon = styled('div', { name, slot: 'root' })(
  ({ theme }) => ({
    width: size,
    height: size,
    borderRadius: 4,
    border: '1px solid transparent',
    backgroundImage: 'linear-gradient(to top, #7ed290, #076e1e);'
  })
);

const RemoveBackgroundIcon = styled('div', { name, slot: 'root' })(
  ({ theme }) => ({
    width: size,
    height: size,
    borderRadius: 4,
    border: '1px solid rgba(0,0,0,0.2)',
    backgroundColor: theme.mixins.backgroundColor('paper')
  })
);

export default function AttachBackgroundStatus(props: BgStatusListProps) {
  const { onClear, onSelectItem } = props;

  const { loaded } = useSelector<GlobalState, AppState>(getBgStatusSelector);

  const [selectedId, setSelectedId] = React.useState(props.selectedId);
  const { dispatch, i18n, dialogBackend } = useGlobal();

  const handleSelect = React.useCallback(
    (item: BgStatusItemShape) => {
      setSelectedId(item.id);
      isFunction(onSelectItem) && onSelectItem(item);
    },
    [onSelectItem]
  );

  const handleClear = React.useCallback(() => {
    setSelectedId(0);

    isFunction(onClear) && onClear();
  }, [onClear]);

  const openPicker = () => {
    dialogBackend.present({
      component: 'bgstatus.dialog.BgStatusPicker',
      props: {
        onSelectItem: handleSelect,
        onClear: handleClear,
        selectedId
      }
    });
  };

  React.useEffect(() => {
    if (!loaded) dispatch({ type: 'bgstatus/LOAD', payload: {} });
  }, [dispatch, loaded]);

  return (
    <Wrapper>
      <Tooltip title={i18n.formatMessage({ id: 'select_background_status' })}>
        <ItemRoot>
          <SelectBackgroundButton
            aria-label="select_background_status"
            data-testid="selectBackgroundStatusButton"
            onClick={openPicker}
            role="button"
          >
            <SelectBackgroundIcon />
          </SelectBackgroundButton>
        </ItemRoot>
      </Tooltip>
      {selectedId > 0 && (
        <Tooltip title={i18n.formatMessage({ id: 'remove_background_status' })}>
          <ItemRoot>
            <RemoveBackgroundButton
              data-testid="removeBackgroundStatusButton"
              aria-label="remove_background_status"
              onClick={handleClear}
              role="button"
            >
              <RemoveBackgroundIcon />
            </RemoveBackgroundButton>
          </ItemRoot>
        </Tooltip>
      )}
    </Wrapper>
  );
}
