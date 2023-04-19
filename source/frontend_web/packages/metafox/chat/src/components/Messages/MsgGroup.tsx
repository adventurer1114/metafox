import { ChatMsgPassProps, MsgGroupShape } from '@metafox/chat/types';
import React from 'react';
import MsgSet from './MsgSet';

interface MsgGroupProps extends ChatMsgPassProps {
  msgGroup: MsgGroupShape;
}

export default function MsgGroup({
  msgGroup,
  disableReact,
  handleAction
}: MsgGroupProps) {
  if (!msgGroup) return null;

  const { items } = msgGroup;

  return items ? (
    <div>
      {items.map((msgSet, i) => (
        <MsgSet
          msgSet={msgSet}
          key={`k.0${i}`}
          disableReact={disableReact}
          handleAction={handleAction}
        />
      ))}
    </div>
  ) : null;
}
