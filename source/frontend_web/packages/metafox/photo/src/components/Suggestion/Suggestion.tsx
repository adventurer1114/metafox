import { useGlobal } from '@metafox/framework';
import { Popper } from '@mui/material';
import * as React from 'react';
import SuggestionList from './ConnectedSuggestionList';

export type Props = {
  classes: any;
  onItemClick: any;
  open: boolean;
  identity: string;
  anchorRef: React.MutableRefObject<HTMLDivElement>;
};

export default function Suggestion({
  classes,
  onItemClick,
  anchorRef,
  identity,
  isFullFriend,
  excludeIds
}: Props) {
  const { i18n } = useGlobal();
  const [text, setText] = React.useState<string>('');

  const onTextChanged = (text: string) => {
    setText(text);
  };

  return (
    <Popper
      id="suggest-friends"
      open={Boolean(anchorRef.current)}
      disablePortal
      anchorEl={anchorRef.current}
      placement="bottom"
      className={classes.popper}
    >
      <div className={classes.inputBaseBox}>
        <input
          type="text"
          placeholder={i18n.formatMessage({ id: 'type_any_name' })}
          onChange={evt => onTextChanged(evt.target.value)}
          autoFocus
          onBlur={e => e.target.focus()}
        />
      </div>
      <SuggestionList
        onItemClick={onItemClick}
        text={text}
        classes={classes}
        identity={identity}
        isFullFriend={isFullFriend}
        excludeIds={excludeIds}
      />
    </Popper>
  );
}
