import { useGetItem } from '@metafox/framework';
import { OnStickerClick, StickerSetShape } from '@metafox/sticker';
import { styled } from '@mui/material/styles';
import React from 'react';
import StickerItem from './StickerItem';

const StickerListContent = styled('ul', {
  name: 'StickerPicker',
  slot: 'ListContent'
})({
  listStyle: 'none none outside',
  margin: 4,
  padding: 0,
  width: 264
});

interface Props {
  identity: string;
  onStickerClick: OnStickerClick;
}

export default function StickerList({ identity, onStickerClick }: Props) {
  const item = useGetItem<StickerSetShape>(identity);

  return (
    <StickerListContent>
      {item.stickers.map(id => (
        <StickerItem
          key={id.toString()}
          identity={id}
          onStickerClick={onStickerClick}
        />
      ))}
    </StickerListContent>
  );
}
